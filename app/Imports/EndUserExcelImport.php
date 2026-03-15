<?php

namespace App\Imports;

use App\Models\EndUser;
use App\Models\Software;
use App\Models\Solution;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;

class EndUserExcelImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * Track processed emails to avoid duplicates within the same import
     */
    protected $processedEmails = [];

    /**
     * Cache for solutions and software to reduce database queries
     */
    protected $solutionCache = [];
    protected $softwareCache = [];

    public function collection(Collection $rows)
    {
        /*
        | Map Zoho columns → Solution names
        | Note: Using the actual column names from your data (with underscores)
        */
        $solutionMap = [
            'a_structure_analysis_service' => 'Structural Analysis',
            'b_system_dynamics_analysis_service' => 'System Dynamics',
            'c_acoustics_analysis_service' => 'Acoustics',
            'd_fluids_analysis_service' => 'Fluids',
            'e_autonomous_analysis_service' => 'Autonomuos',
            'f_vmc_analysis_service' => 'VM&C',
            'g_icme_analysis_service' => 'ICME (Materials)',
        ];

        /*
        | Map Zoho columns → Software names
        | Note: Using the actual column names from your data
        */
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
            // Additional software from your data that might be needed
            't638_ansys_fluent' => 'Ansys Fluent',
            't632_ansys' => 'Ansys',
            't642_abaqus' => 'Abaqus',
            't646_solidworks' => 'SolidWorks',
            't653_hyper_works' => 'HyperWorks',
            't634_hypermesh' => 'HyperMesh',
            't650_matlab' => 'MATLAB',
        ];

        foreach ($rows as $row) {
            // Skip if no email
            if (empty($row['email'])) {
                Log::warning('Skipping row with no email', ['row' => $row]);
                continue;
            }

            // Check for duplicate within the same import
            $email = strtolower(trim($row['email']));
            if (in_array($email, $this->processedEmails)) {
                Log::info('Skipping duplicate email within same import', ['email' => $email]);
                continue;
            }

            DB::transaction(function () use ($row, $solutionMap, $softwareMap, $email) {
                try {
                    /*
                    | Find or Create End User based on email
                    */
                    $endUser = EndUser::where('email', $email)->first();

                    if ($endUser) {
                        // Update existing user if needed
                        Log::info('Updating existing user', ['email' => $email]);
                        $endUser->update([
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
                        ]);
                    } else {
                        // Create new user
                        Log::info('Creating new user', ['email' => $email]);
                        $endUser = EndUser::create([
                            'first_name' => $this->getValue($row, 'a002_first_name'),
                            'last_name' => $this->getValue($row, 'a003_last_name'),
                            'email' => $email,
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
                        ]);
                    }

                    // Mark this email as processed
                    $this->processedEmails[] = $email;

                    /*
                    | Attach Solutions
                    | First detach existing ones if updating, then attach new ones
                    */
                    $solutionIds = [];
                    foreach ($solutionMap as $column => $solutionName) {
                        if ($this->isTrue($this->getValue($row, $column))) {
                            $solution = $this->getSolution($solutionName);
                            if ($solution) {
                                $solutionIds[] = $solution->id;
                            }
                        }
                    }
                    
                    if (!empty($solutionIds)) {
                        // Sync without detaching to preserve existing relationships
                        $endUser->solutions()->syncWithoutDetaching($solutionIds);
                    }

                    /*
                    | Attach Softwares
                    */
                    $softwareAttachments = [];
                    foreach ($softwareMap as $column => $softwareName) {
                        if ($this->isTrue($this->getValue($row, $column))) {
                            $software = $this->getSoftware($softwareName);
                            if ($software) {
                                $softwareAttachments[$software->id] = ['level' => 'User'];
                            }
                        }
                    }
                    
                    if (!empty($softwareAttachments)) {
                        // Sync without detaching to preserve existing relationships
                        $endUser->softwares()->syncWithoutDetaching($softwareAttachments);
                    }

                } catch (\Exception $e) {
                    Log::error('Error processing row', [
                        'email' => $email,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            });
        }
    }

    /**
     * Safely get value from row array
     */
    protected function getValue($row, $key, $default = null)
    {
        // Remove dots and replace with underscores for array access
        $key = str_replace('.', '_', $key);
        return $row[$key] ?? $default;
    }

    /**
     * Check if a value is true (handles strings, booleans, etc.)
     */
    protected function isTrue($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['true', '1', 'yes', 'on']);
        }
        
        if (is_numeric($value)) {
            return (bool) $value;
        }
        
        return !empty($value);
    }

    /**
     * Get solution from cache or database
     */
    protected function getSolution($name)
    {
        if (!isset($this->solutionCache[$name])) {
            $this->solutionCache[$name] = Solution::where('name', $name)->first();
        }
        return $this->solutionCache[$name];
    }

    /**
     * Get software from cache or database
     */
    protected function getSoftware($name)
    {
        if (!isset($this->softwareCache[$name])) {
            $this->softwareCache[$name] = Software::where('name', $name)->first();
        }
        return $this->softwareCache[$name];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}