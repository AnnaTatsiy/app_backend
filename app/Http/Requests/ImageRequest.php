<?php


namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest{

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'image' => 'required',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }

    // вывод сообщений
    public function messages()
    {
        return [
            'image' => 'Изображение не было выбрано',
            'image.*.max' => "Изображение слишком большое",
            'image.*' => "Требуется расширение файла jpg, png, jpeg, gif, svg"
        ];
    }
}
