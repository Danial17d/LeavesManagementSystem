<?php

namespace App\Http\Requests;

use App\Enums\PermissionType;
use App\Rules\IsHasBalance;
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
            'attachment' => ['nullable', 'array'],
        ];
    }
    public function messages(): array{
        return [

        ];
    }
}
