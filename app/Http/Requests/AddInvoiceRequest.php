<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vapData.projectId' => 'required',
            'vapData.bonId' => 'required',
            'vapData.factuurbedrag' => 'required',
            'vapData.omschrijving' => 'required',
            'vapData.krediettermijn' => 'required',
            'vapData.klant' => 'required',
            'data.relatie.id' => 'required',
            'data.VerkooporderBtwIngaveModel' => 'required',
            'data.procesStatus' => 'required',
            'data.datum' => 'required',
            'data.krediettermijn' => 'required',
            'data.omschrijving' => 'required',
            'data.verkoopordersjabloon.id' => 'required'
        ];
    }
}
