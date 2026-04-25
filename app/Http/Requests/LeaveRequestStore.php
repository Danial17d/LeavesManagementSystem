<?php

namespace App\Http\Requests;

use App\Enums\PermissionType;
use App\Models\LeaveRequest;
use App\Rules\IsHasBalance;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LeaveRequestStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows(PermissionType::LeaveRequestCreate);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leave_type' => ['required','exists:leave_types,name'],
            'days_requested' => ['required','integer','min:1',new IsHasBalance()],
            'start_date' => ['required','date'],
            'end_date' => ['required','date','after_or_equal:start_date'],
            'reason' => ['nullable','string' , 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->user()?->loadMissing('structure.parent')->isChiefExecutive()) {
                $validator->errors()->add('leave_request', 'The CEO cannot request leave because no higher approver is assigned.');
            }

            $activeLeave = LeaveRequest::where('user_id', $this->user()->id)->active()->first();

            if ($activeLeave) {
                $until = Carbon::parse($activeLeave->to)->format('M d, Y');
                $validator->errors()->add('leave_request', "You already have an active leave request running until {$until}. You cannot submit a new one until it ends.");
            }
        });
    }

    public function messages(): array{
        return [

        ];
    }
}
