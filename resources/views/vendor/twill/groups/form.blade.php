@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        name="description"
        label="Description"
        :maxlength="250"
        placeholder="Enter the description for the group"
        type="textarea"
        :rows="3"
    />

    <x-twill::browser
        module-name="users"
        name="users"
        label="Users"
        :max="999"
    />

    @if(\A17\Twill\Facades\TwillPermissions::levelIs(\A17\Twill\Enums\PermissionLevel::LEVEL_ROLE_GROUP))
        <x-twill::fieldRows title="Content permissions">
            <x-twill::checkbox
                name="manage-modules"
                label="Manage all modules"
            />

            <x-twill::formConnectedFields field-name="manage-modules"
                                          :fieldValues="false"
            >
                @foreach($permissionModules as $permissionModuleName => $moduleItems)
                    <x-twill::select
                        :name="'module_' . $permissionModuleName . '_permissions'"
                        :label="ucfirst($permissionModuleName) . ' permissions'"
                        placeholder="Select a permission"
                        :options="[
                            [
                                'value' => 'none',
                                'label' => 'None'
                            ],
                            [
                                'value' => 'view-module',
                                'label' => 'View ' . $permissionModuleName
                            ],
                            [
                                'value' => 'edit-module',
                                'label' => 'Edit ' . $permissionModuleName
                            ]
                        ]"
                    />
                @endforeach
            </x-twill::formConnectedFields>
        </x-twill::fieldRows>
    @endif

    @if(config('twill.support_subdomain_admin_routing'))
        <x-twill::fieldRows title="Subdomain access">
            @foreach(config('twill.app_names') as $subdomain => $subdomainTitle)
                <x-twill::checkbox
                    :name="'subdomain_access_' . $subdomain"
                    :label="$subdomainTitle"
                />
            @endforeach
        </x-twill::fieldRows>
    @endif
@stop

@if(\A17\Twill\Facades\TwillPermissions::levelIs(\A17\Twill\Enums\PermissionLevel::LEVEL_ROLE_GROUP_ITEM))
    @can('edit-user-groups')
        @section('fieldsets')
            @foreach($permissionModules as $permissionModuleName => $moduleItems)
                <a17-fieldset title='{{ ucfirst($permissionModuleName) . " Permissions"}}' id='{{ $permissionModuleName }}'>
                    <x-twill::select-permissions
                        :items-in-selects-tables="$moduleItems"
                        label-key="title"
                        :name-pattern="$permissionModuleName . '_%id%_permission'"
                    />
                </a17-fieldset>
            @endforeach
        @stop
    @endcan
@endif
