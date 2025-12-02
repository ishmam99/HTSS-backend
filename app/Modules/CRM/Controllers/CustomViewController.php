<?php

namespace Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\CustomView;
use Modules\CRM\Models\CustomViewCondition;
use Modules\CRM\Models\CustomViewGroup;

class CustomViewController extends Controller
{

    public function index()
    {
        $views = CustomView::where('user_id',auth()->id())->get();
        return response()->json($views);
    }

   public function show($id)
   {
    $view = CustomView::with('rootGroup.childrenRecursive.conditions')->find($id);
    return response()->json($view);
   }

   public function store(Request $request)
{
    DB::transaction(function () use ($request) {

        $view = CustomView::create([
            'name' => $request->name,
            'module' => $request->module,
            'user_id' => auth()->id()
        ]);

        $this->saveGroup($view->id, null, $request->root_group);
    });

    return response()->json(['message' => 'Custom view created']);
}
private function saveGroup($customViewId, $parentId, $groupData)
{
    $group = CustomViewGroup::create([
        'custom_view_id' => $customViewId,
        'parent_id' => $parentId,
        'join_type' => $groupData['join_type'] ?? 'AND',
        'order' => $groupData['order'] ?? 0
    ]);

    if (!empty($groupData['conditions']) && $group) {
        foreach ($groupData['conditions'] as $index => $cond) {
            CustomViewCondition::create([
                'custom_view_group_id' => $group->id,
                'field' => $cond['field'],
                'operator' => $cond['operator'],
                'value' => $cond['value'],
                'order' => $index
            ]);
        }
    }

    // Save nested groups (recursion)
    if (!empty($groupData['groups']) && $group) {
        foreach ($groupData['groups'] as $childGroup) {
            $this->saveGroup($customViewId, $group->id, $childGroup);
        }
    }
}
public function destroy($id)
{

    $customView = CustomView::where('id',$id)->delete();
    return response()->json('Data deleted successfully');
}

}
