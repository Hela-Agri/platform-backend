<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status=Status::where('code','active')->first();
        if(!$status)
        return;

        $modules=[
            array('name'=>'user','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>true,'activate'=>true,'upload'=>false),
            array('name'=>'role','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'farmer','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>true,'activate'=>true,'upload'=>false),
            array('name'=>'farm','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'farm_activity','has_approve'=>true,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'service','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'product','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'category','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'cohort','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'rate_card','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'loan','has_approve'=>true,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'loan_product','has_approve'=>true,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'wallet','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'withdraw','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'deposit','has_approve'=>true,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'payment','has_approve'=>true,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'setting','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'center','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'site_visit','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'yield','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'report','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
            array('name'=>'activity_logs','has_approve'=>false,'has_print'=>false,'has_download'=>false,'deactivate'=>false,'activate'=>false,'upload'=>false),
        ];

        $permissions=array();

        Schema::disableForeignKeyConstraints();

        foreach($modules as $module){
            Module::updateOrcreate(
                ['name'=>$module['name']],
                $module
            );

            $permissions[]= ['name' => 'List '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'list.'.$module['name'],'description'=>'List '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            $permissions[]= ['name' => 'View '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'view.'.$module['name'],'description'=>'View '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            $permissions[]= ['name' => 'Create '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'create.'.$module['name'],'description'=>'Create '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            $permissions[]= ['name' => 'Edit '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'edit.'.$module['name'],'description'=>'Edit '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            $permissions[]= ['name' => 'Delete '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'delete.'.$module['name'],'description'=>'Delete '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            if($module['has_approve']){
                $permissions[]= ['name' => 'Approve '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'approve.'.$module['name'],'description'=>'Approve  '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            }
            if($module['has_print']){
                $permissions[]= ['name' => 'Print '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'print.'.$module['name'],'description'=>'Print  '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            }
            if($module['has_download']){
                $permissions[]= ['name' => 'Download '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'download.'.$module['name'],'description'=>'Download  '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            }
            if($module['deactivate']){
                $permissions[]= ['name' => 'Deactivate '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'deactivate.'.$module['name'],'description'=>'Deactivate  '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            }
            if($module['activate']){
                $permissions[]= ['name' => 'Activate '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'activate.'.$module['name'],'description'=>'Activate  '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            }
            if($module['upload']){
                $permissions[]= ['name' => 'Upload '.ucfirst(str_replace('_',' ',$module['name'])), 'code' => 'upload.'.$module['name'],'description'=>'Upload  '.str_replace('_',' ',$module['name']), 'status_id' => $status->id ];
            }

        }

        Permission::truncate();
        RolePermission::truncate();

        Schema::enableForeignKeyConstraints();

        $role= DB::table('roles')->where('code', 'super_administrator')->first();

        foreach($permissions as  $permission){


            $perm=Permission::updateOrcreate(
                [
                    'code'=>$permission['code']
                ],
                $permission
            );

            if($role){

                RolePermission::updateorCreate(
                [
                    'role_id'=>$role->id,
                    'permission_id'=>$perm->id,
                ]
                );
            }
        }
        $role= DB::table('roles')->where('code', 'administrator')->first();

        foreach($permissions as  $permission){


            $perm=Permission::updateOrcreate(
                [
                    'code'=>$permission['code']
                ],
                $permission
            );

            if($role){

                RolePermission::updateorCreate(
                [
                    'role_id'=>$role->id,
                    'permission_id'=>$perm->id,
                ]
                );
            }
        }
    }
}
