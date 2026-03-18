<?php

namespace App\Imports;

use App\Models\EndUser;
use App\Models\Software;
use App\Models\Solution;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EndUserExcelImport implements 
    ToCollection, 
    WithHeadingRow, 
    WithChunkReading, 
    ShouldQueue
{
    /**
     * Cache for solutions and software
     */
    protected $solutionCache = [];
    protected $softwareCache = [];

    public function __construct()
    {
        // Preload all solutions and software once per job
        $this->solutionCache = Solution::pluck('id', 'name')->toArray();
        $this->softwareCache = Software::pluck('id', 'name')->toArray();
    }

    public function collection(Collection $rows)
    {
        $solutionMap = [
            'a_structure_analysis_service' => 'Structural Analysis',
            'b_system_dynamics_analysis_service' => 'System Dynamics',
            'c_acoustics_analysis_service' => 'Acoustics',
            'd_fluids_analysis_service' => 'Fluids',
            'e_autonomous_analysis_service' => 'Autonomuos',
            'f_vmc_analysis_service' => 'VM&C',
            'g_icme_analysis_service' => 'ICME (Materials)',
        ];

        $softwareMap = [
            'd1_str_104_aoi_msc_nastran' => 'Nastran',
            'd1_106_str_aoi_msc_patran' => 'Patran',
            'd1_101_str_aoi_adams' => 'Adams',
            'd1_105_str_aoi_msc_apex' => 'MSC Apex',
            'd1_107_str_aoi_dytran' => 'Dytran',
            'd1_108_str_aoi_simmanager' => 'Sim Manager',
            'd1_111_sd_aoi_romax' => 'Romax',
            'd1_121_act_aoi_actran' => 'Actran',
            'd1_131_cfd_aoi_msc_cradle_cfd' => 'MSC Cradle CFD',
            'd1_132_cfd_aoi_msc_cosim' => 'MSCCoSim',
            'd1_143_auto_aoi_vtd' => 'VTD',
            'd1_141_auto_aoi_vtd_scale' => 'VTDScale',
            'd1_142_auto_aoi_cloud' => 'Cloud',
            'd1_152_vmc_aoi_fti_formingsuite' => 'FTI FormingSuite',
            'd1_153_vmc_aoi_simufact' => 'Simufact',
            'd1_161_icme_aoi_material_center' => 'MaterialCenter',
            'd1_162_icme_aoi_digimat' => 'Digimat',
            'd1_163_icme_aoi_material_center_databanks' => 'MaterialCenterDatabanks',
            't638_ansys_fluent' => 'Ansys Fluent',
            't632_ansys' => 'Ansys',
            't642_abaqus' => 'Abaqus',
            't646_solidworks' => 'SolidWorks',
            't653_hyper_works' => 'HyperWorks',
            't634_hypermesh' => 'HyperMesh',
            't650_matlab' => 'MATLAB',
        ];

        DB::transaction(function () use ($rows, $solutionMap, $softwareMap) {
            foreach ($rows as $row) {

                if (empty($row['email'])) {
                    continue;
                }

                $email = strtolower(trim($row['email']));

                try {
                    /*
                    | Find or Create End User
                    */
                    $endUser = EndUser::updateOrCreate(
                        ['email' => $email],
                        [
                            'first_name' => $this->getValue($row, 'a002_first_name'),
                            'last_name' => $this->getValue($row, 'a003_last_name'),
                            'secondary_email' => $this->getValue($row, 'secondary_email'),
                            'state' => $this->getValue($row, 'a009_state'),
                            'zip_code' => $this->getValue($row, 'a010_zip_code'),
                            'city' => $this->getValue($row, 'a008_city'),
                            'country' => $this->getValue($row, 'a011_country'),
                            'direct_phone' => $this->getValue($row, 'a004_direct_phone'),
                            'cell_phone' => $this->getValue($row, 'a005_cell_phone'),
                            'department' => $this->getValue($row, 'department'),
                            'discipline' => $this->getValue($row, 'discipline'),
                            'current_industry' => $this->getValue($row, 'd098_current_industry'),
                            'status' => 1
                        ]
                    );

                    /*
                    | Attach Solutions
                    */
                    $solutionIds = [];

                    foreach ($solutionMap as $column => $solutionName) {
                        if ($this->isTrue($this->getValue($row, $column))) {
                            if (isset($this->solutionCache[$solutionName])) {
                                $solutionIds[] = $this->solutionCache[$solutionName];
                            }
                        }
                    }

                    if (!empty($solutionIds)) {
                        $endUser->solutions()->syncWithoutDetaching($solutionIds);
                    }

                    /*
                    | Attach Softwares
                    */
                    $softwareAttachments = [];

                    foreach ($softwareMap as $column => $softwareName) {
                        if ($this->isTrue($this->getValue($row, $column))) {
                            if (isset($this->softwareCache[$softwareName])) {
                                $softwareAttachments[$this->softwareCache[$softwareName]] = ['level' => 'User'];
                            }
                        }
                    }

                    if (!empty($softwareAttachments)) {
                        $endUser->softwares()->syncWithoutDetaching($softwareAttachments);
                    }

                } catch (\Exception $e) {
                    Log::error('Row failed', [
                        'email' => $email,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });
    }

    protected function getValue($row, $key, $default = null)
    {
        $key = str_replace('.', '_', $key);
        return $row[$key] ?? $default;
    }

    protected function isTrue($value)
    {
        if (is_bool($value)) return $value;

        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['true', '1', 'yes', 'on']);
        }

        if (is_numeric($value)) return (bool) $value;

        return !empty($value);
    }

    public function chunkSize(): int
    {
        return 100; // safer for shared hosting
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Import job failed', [
            'error' => $exception->getMessage()
        ]);
    }
}