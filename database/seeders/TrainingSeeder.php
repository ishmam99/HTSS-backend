<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\Software;
use App\Models\Solution;
use App\Models\Training;
use App\Models\TrainingCourse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TrainingCourse::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
       $industries = Industry::all();

        $trainingTypes = [
            'Onsite' => 'OS',
            'Online' => 'OL',
            'Seminar' => 'SE',
            'Conference' => 'CN',
        ];

        $solutions =  ['Structural Analysis','System Dynamics','Acouastics','Fluids','Autonomuos','VM&C','ICME (Materials)'];
$softwareSolutionMap = [
    'Structural Analysis' => ['Patran','Nastran','Marc','MSC Fatigue','Adams','Sim Manager','Dytran','MSC Apex'],
    'System Dynamics'     => ['Romax','Easy 5','Elements'],
    'Acouastics'          => ['Actran'],
    'Fluids'              => ['MSC Cradle CFD','MSCCoSim'],
    'Autonomuos'          => ['VTDScale','VTD','Cloud'],
    'VM&C'                => ['ODYSSEE','Simufact','FTI FormingSuite'],
    'ICME (Materials)'    => ['MaterialCenter','Digimat','MaterialCenterDatabanks']
];

        $levels = ['Basic', 'Intermidiate', 'Advance'];

        // Create base data
        foreach ($industries as $industry) {
           $name = $industry->name;

            foreach ($trainingTypes as $typeName => $typeCode) {


                foreach ($solutions as $solutionName) {
                         $solution = Solution::firstOrCreate(['name' => $solutionName]);
                        $solutionshortCode = collect(explode(' ', $solution->name))
            ->reject(fn($word) => $word === '&') // skip "&"
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->join('');
                   $solution = Solution::firstOrCreate(['name' => $solutionName]);
                    $softwaresForSolution = $softwareSolutionMap[$solutionName];

                    foreach ($softwaresForSolution as $softwareName) {
                        $software = Software::firstOrCreate(['name' => $softwareName]);

                        //    $software = Solution::firstOrCreate(['name' => $softwareName]);
                           $softwareName = $software->name;
                        //    dd($software);
                        foreach ($levels as $index => $levelName) {
                            if($softwareName== 'MSC Fatigue')
                            {
                                $softwareShort = 'FAT';
                            }
                            else if( $softwareName== 'MSC Cradle CFD')
                            {
                                $softwareShort = 'CRA';
                            }
                            else if($softwareName == 'MSCCoSim')
                            {
                                $softwareShort = 'COS';
                            }
                            else
                            $softwareShort = strtoupper(Str::substr($softwareName, 0, 3));
                            $shortIndustry =collect(explode(' ', $name))
                                ->reject(fn($word) => $word === '&') // skip "&"
                                ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                ->join('');
                            $courseId = "{$shortIndustry}-{$solutionshortCode}-{$softwareShort}-{$typeCode}" . (101 + $index * 100);
                            $title = "{$shortIndustry}_{$solution->name}_{$softwareName}_{$levelName}";

                            TrainingCourse::create([
                                'industry_id' => $industry->id,
                                'training_type' => $typeName,
                                'solution_id' => $solution->id,
                                'software_id' => $software->id,
                                'training_level' => $levelName,
                                'title' => $title,
                                'course_id' => $courseId,
                                'course_code' => $typeCode,
                                'duration' => '1 Day'
                            ]);
                        }
                    }
                }
            }
        }
    }
    }

