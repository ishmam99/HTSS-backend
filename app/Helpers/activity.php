<?php

use Illuminate\Support\Facades\Auth;
use Modules\CRM\Models\Activity as ModelsActivity;

if (! function_exists('logActivity')) {
    /**
     * @param string $action
     * @param string|null $module
     * @param int|null $recordId
     * @param array|null $details
     * @param string|null $meta
     * @return Activity
     */
    function logActivity(string $action, ?string $module = null, $recordId = null, ?array $details = null, ?string $meta = null,?object $user=null)
    {
        if(!$user)
        $user = Auth::user();

        return ModelsActivity::create([
            'user_id'    => $user ? $user->id : null,
            'action'     => $action,
            'module'     => $module,
            'record_id'  => $recordId,
            'details'    => $details ? json_encode($details) : null,
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
            'meta'       => $meta,
        ]);
    }
}
