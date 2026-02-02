<?php
namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\SuccessTeam;
use App\Models\SuccessTeamCompany;
use App\Models\TeamActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuccessTeamController extends Controller
{
   public function index()
{

    return SuccessTeam::with([
        'members',
        'owner',
        'companies' => function ($q) {
            $q->withCount('customers');
        }])->withCustomersCount()->paginate(10);
}

    // Create new team
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'user_id'        => 'required|exists:users,id',
            'status'         => 'nullable|in:0,1',
            'company_id'     => 'nullable|exists:companies,id',
            'members'        => 'nullable|array',
            'members.*.id'   => 'required|exists:users,id',
            'members.*.role' => 'required|string|max:255',
            'companies'      => 'nullable|array',
            'companies.*'    => 'exists:companies,id',
        ]);

        $team = SuccessTeam::create($request->only('name', 'user_id', 'status', 'company_id'));

        // Assign members
        if ($request->has('members')) {
            $members = [];
            foreach ($request->members as $member) {
                $members[$member['id']] = ['role' => $member['role']];
            }
            $team->members()->sync($members);
        }

        // Assign companies
        if ($request->has('companies')) {
            $team->companies()->sync($request->companies);
        }

        return response()->json($team->load(['members', 'companies', 'owner']));
    }

    // Show single team
    public function show($id)
    {
        $team = SuccessTeam::with(['members','company.customers.user', 'companies.customers.user','companies.customers.industry', 'owner'])->findOrFail($id);
        return response()->json($team);
    }

    // Update team info (name, owner, status, company_id)
    public function update(Request $request, $id)
    {
        $team = SuccessTeam::findOrFail($id);

        $request->validate([
            'name'       => 'nullable|string|max:255',
            'user_id'    => 'nullable|exists:users,id',
            'status'     => 'nullable|in:0,1',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $team->update($request->only('name', 'user_id', 'status', 'company_id'));

        return response()->json($team->load(['members', 'companies', 'owner']));
    }

    // Delete team
    public function destroy($id)
    {
        $team = SuccessTeam::findOrFail($id);
        $team->delete();

        return response()->json(['message' => 'SuccessTeam deleted']);
    }

    // Assign members and companies to an existing team
    public function assign(Request $request, $id)
    {
        $team = SuccessTeam::findOrFail($id);

        $request->validate([
            'members'        => 'nullable|array',
            'members.*.id'   => 'required|exists:users,id',
            'members.*.role' => 'required|string|max:255',
            'companies'      => 'nullable|array',
            'companies.*'    => 'exists:companies,id',
        ]);

        // Assign members
        if ($request->has('members')) {
            $members = [];
            foreach ($request->members as $member) {
                $members[$member['id']] = ['role' => $member['role']];
            }
            TeamActivity::create([
                'success_team_id' => $team->id,
                'user_id' => Auth::id(),
                'activity_type' => 'Added Members',
                'description' => 'New member added to the team.',
            ]);
            $team->members()->sync($members);
        }

        // Assign companies
        if ($request->has('companies')) {
            TeamActivity::create([
                'success_team_id' => $team->id,
                'user_id' => Auth::id(),
                'activity_type' => 'Assigned Companies',
                'description' => 'New company assigned to the team.',
            ]);
            $team->companies()->sync($request->companies);
        }

        return response()->json($team->load(['members', 'companies', 'owner']));
    }

    public function getCustomersBySuccessTeam($success_team_id)
    {
        $companies = SuccessTeamCompany::where('success_team_id', $success_team_id)
            ->pluck('company_id');
        $customers = Customer::with('user')
            ->whereIn('company_id', $companies)->get();
        return CustomerResource::collection($customers);
    }

    public function mySuccessTeams(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $successTeams = SuccessTeam::with(['members', 'companies','company.customers', 'owner'])
            ->whereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->paginate(10);
            if(auth()->user()->role == 'customer_success_management_manager')
                {
                     $successTeams = SuccessTeam::with(['members', 'companies','company.customers', 'owner'])
            ->where('user_id',auth()->id())
            ->paginate(10);
                }

        return response()->json([
            'success' => true,
            'message' => 'Success teams retrieved successfully',
            'data'    => $successTeams,
        ]);
    }

    public function getSuccessTeamCompaniesCustomers($success_team_id)
    {
        $successTeam = SuccessTeam::with([
            'companies.customers.user',
        ])->findOrFail($success_team_id);

        return response()->json([
            'success_team_id' => $successTeam->id,
            'companies'       => $successTeam->companies->map(function ($company) {
                return [
                    'id'        => $company->id,
                    'name'      => $company->name,
                    'address'   => $company->address,
                    'email'     => $company->email,
                    'phone'     => $company->phone,
                    'website'   => $company->website,
                    'customers' => CustomerResource::collection($company->customers),
                ];
            }),
        ]);
    }

    public function getSuccessTeamCompanies($success_team_id)
    {
        $successTeam = SuccessTeam::with([
            'companies',
        ])->findOrFail($success_team_id);

        return response()->json([
            'success_team_id' => $successTeam->id,
            'companies'       => $successTeam->companies->map(function ($company) {
                return [
                    'id'      => $company->id,
                    'name'    => $company->name,
                    'address' => $company->address,
                    'email'   => $company->email,
                    'phone'   => $company->phone,
                    'website' => $company->website,
                ];
            }),
        ]);
    }

}
