<?php

namespace Modules\Members\Console;

use Modules\Members\Entities\MemberSelectOption;
use Illuminate\Console\Command;

class PopulateMemberSelectOptions extends Command
{
    protected $name = 'members:populate-member-select-options';

    public function handle()
    {
        MemberSelectOption::truncate();

        $this->info('Resetting Table Data');

        $this->seed('disciplines', [
            'Coarse',
            'Match',
            'Sea',
            'Carp',
            'Game',
            'Recreation',
            'Specimen'
        ]);

        $this->seed('division', [
            'Division 1',
            'Division 2'
        ]);

        $this->seed('region', [
            'Cornwall',
            'East Anglia',
            'Essex',
            'Isle of Wight',
            'Midlands',
            'North East',
            'North West',
            'Northumberland',
            'Severn',
            'Southern',
            'South East',
            'Sussex',
            'Wessex',
            'Wyvern',
            'Yorkshire',
            'Wales',
            'Scotland',
            'Northern Ireland',
            'Republic of Ireland'
        ]);

        $this->seed('gender', [
            'Male',
            'Female',
            'Prefer not to say'
        ]);

        $this->seed('ethnicity', [
            'White - British',
            'White - Irish',
            'White - Other',
            'Mixed - White and Black Caribbean',
            'Mixed - White and Asian',
            'Mixed - Other',
            'Asian or Asian British - Indian',
            'Asian or Asian British - Pakistani',
            'Asian or Asian British - Bangladeshi',
            'Asian or Asian British - Other',
            'Black or Black British - Caribbean',
            'Black or Black British - African',
            'Black or Black British - Other',
            'Chinese',
            'Other Ethnic Group'
        ]);

        $this->seed('disability_1', [
            'Yes',
            'No',
            'Prefer not to say'
        ]);

        $this->seed('disability_2', [
            'Yes',
            'No',
            'Prefer not to say'
        ]);

        $this->seed('disability_3', [
            'Long term pain',
            'Chronic health condition',
            'Mobility',
            'Dexterity',
            'Mental health',
            'Visual',
            'Breathing',
            'Memory',
            'Hearing',
            'Learning',
            'Speech',
            'Behavioural',
            'Other',
            'None of these',
            'Prefer not to say'
        ]);

        $this->seed('registration_source', [
            'Internet Search',
            'Online Advertising',
            'Social Media',
            'Met at a Show',
            'Referred by Another Member or Angler',
            'Referred by Club or Fishery',
            'Angling Publication',
            'Advertising at Tackle Shop',
            'Attend an Angling Trust Event',
            'Other'
        ]);

        $this->seed('reason_for_joining', [
            'To support Angling Trust campaigns',
            'Joining offer and membership benefits',
            'To take part in Angling Trust competitions',
            'Other'
        ]);

        $this->seed('joining_gift', [
            '3 month subscription - Angling Times',
            '3 month subscription - Improve Your Coarse Fishing',
            '3 month subscription - Sea Angler',
            '3 month subscription - Trout & Salmon',
            'Dynamite - Coarse Pack',
            'Dynamite - Carp Pack',
            '£29 Voucher to spend on tackle'
        ]);

        $this->seed('fishing_rights', [
            'Own',
            'Lease',
            'Licence',
            'None'
        ]);

        $this->seed('relevant_catchments', [
            'Broadland Rivers',
            'Cam and Ely Ouse (including South Level)',
            'Combined Essex',
            'East Suffolk',
            'Nene',
            'North Norfolk',
            'North West Norfolk',
            'Old Bedford including the Middle Level',
            'Upper and Bedford Ouse',
            'Welland',
            'Witham',
            'Middle Dee',
            'Tidal Dee',
            'Upper Dee',
            'Aire and Calder',
            'Derbyshire Derwent',
            'Derwent (Humber)',
            'Don and Rother',
            'Dove',
            'Esk and Coast',
            'Hull and East Riding',
            'Idle and Torne',
            'Louth, Grimsby and Ancholme',
            'Lower Trent and Erewash',
            'Soar',
            'Staffordshire Trent Valley',
            'Swale, Ure, Nidd and Upper Ouse',
            'Tame, Anker and Mease',
            'Wharfe and Lower Ouse',
            'Alt and Crossens',
            'Derwent North West',
            'Douglas',
            'Irwell',
            'Kent and Leven',
            'Lune',
            'Mersey Estuary',
            'Ribble',
            'South West Lakes',
            'Upper Mersey',
            'Weaver Gowy',
            'Wyre',
            'Northumberland Rivers',
            'Tees',
            'Tyne',
            'Wear',
            'Bristol Avon and North Somerset Streams',
            'Severn Uplands',
            'Severn Vale',
            'Shropshire Middle Severn',
            'South East Valleys',
            'Teme',
            'Usk',
            'Warwickshire Avon',
            'Worcestershire Middle Severn',
            'Wye',
            'Eden and Esk',
            'Till',
            'Waver Wampool',
            'Adur and Ouse',
            'Arun and Westren Streams',
            'Cuckmere and Pevensey Levels',
            'East Hampshire',
            'Isle of Wight',
            'New Forest',
            'Rother',
            'Dtour',
            'Test and Itchen',
            'Dorset',
            'East Devon',
            'Hampshire Avon',
            'North Cornwall, Seaton , Looe and Fowey',
            'North Devon',
            'South & Wesy Somerset',
            'South Devon',
            'Tamar',
            'West Cornwall and the Fal',
            'Cherwell',
            'Colne',
            'Cotswolds',
            'Darent',
            'Kennet and Pang',
            'Loddon',
            'London',
            'Maidenhead to Sunbury',
            'Medway',
            'Mole',
            'North Kent',
            'Roding, Beam and Ingrebourne',
            'South Essex',
            'Thame and South Chilterns',
            'Thames (Tidal)',
            'Upper Lee',
            'Vale of White Horse',
            'Wey',
            'Solway Tweed',
            'Conwy and Clwyd',
            'Loughor to Taf',
            'North West Wales',
            'Ogmore to Tawe',
            'South West Wales'
        ]);

        $this->seed('title', [
            'Mr',
            'Mrs',
            'Miss',
            'Master',
            'Ms',
            'Mx',
            'Dr',
            'Sir',
            'Lord'
        ]);

        $this->seed('coaching_level', [
            'Level 1',
            'Level 2'
        ]);

        $this->seed('water_type', [
            'River',
            'Stillwater',
            'Canal',
            'Sea'
        ]);

        $this->info('Options Populated');
    }

    private function seed($type, $items)
    {
        $this->line('Populating ' . upper_case($type));

        foreach ($items as $name) {
            MemberSelectOption::create([
                'type' => $type,
                'name' => $name
            ]);

            $this->line(' - created ' . $name);
        }
    }
}
