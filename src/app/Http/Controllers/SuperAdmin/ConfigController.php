<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GlobalConfigUpdateRequest;
use App\Models\GlobalSlotConfig;
use App\Services\AuditLogService;

class ConfigController extends Controller
{
    public function edit()
    {
        $config = GlobalSlotConfig::current();
        
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];
        
        return view('super-admin.config.edit', compact('config', 'daysOfWeek'));
    }
    
    public function update(GlobalConfigUpdateRequest $request)
    {
        $config = GlobalSlotConfig::current();
        
        $oldValues = $config->toArray();
        
        $data = $request->validated();

    // ADD THIS LOGGING
    \Log::info('Config Update Attempt', [
        'validated_data' => $data,
        'old_values' => $oldValues,
    ]);

        $data['updated_by'] = auth()->id();
        
    // Normalize time formats to H:i:s for database
    if (isset($data['non_pharmacy_start_time'])) {
        $data['non_pharmacy_start_time'] = \Carbon\Carbon::parse($data['non_pharmacy_start_time'])->format('H:i:s');
    }
    if (isset($data['non_pharmacy_end_time'])) {
        $data['non_pharmacy_end_time'] = \Carbon\Carbon::parse($data['non_pharmacy_end_time'])->format('H:i:s');
    }
    if (isset($data['pharmacy_start_time'])) {
        $data['pharmacy_start_time'] = \Carbon\Carbon::parse($data['pharmacy_start_time'])->format('H:i:s');
    }
    if (isset($data['pharmacy_end_time'])) {
        $data['pharmacy_end_time'] = \Carbon\Carbon::parse($data['pharmacy_end_time'])->format('H:i:s');
    }
    
    $config->update($data);
    
    $newValues = $config->fresh()->toArray();
    
    AuditLogService::logConfigChange($config, $oldValues, $newValues);
    
    return redirect()->route('super-admin.config.edit')
        ->with('success', 'System configuration updated successfully. Changes will take effect immediately.');
}
}
