<?php

namespace App\Actions\Fortify;

use App\Rules\Alpha_num_extras;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Intervention\Image\Facades\Image;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:25', 'min:2', new Alpha_num_extras],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'min:5',
                Rule::unique('users')->ignore($user->id),
            ],
            'description' => ['string', 'max:255', ],
            'avatar' =>['mimes:jpg,jpeg,png,webp', 'max:2000'], // 2000kb = 2mb
        ])->validateWithBag('updateProfileInformation');

        /* [avatar] delete previous image and make new name */
        if($input['avatar'] ?? false) {
            $previous_image_name = $user->avatar;
            if($previous_image_name) {
                Storage::disk('avatars')->delete($previous_image_name);
            }

            $image_file = $input['avatar'];
            $image_name = time() .'-'. uniqid() . '.webp';
            $input['avatar'] = $image_name;
        }

        /* Save to Db */
        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'description' => $input['description'],
                'avatar' => $input['avatar'],
            ])->save();
        }

        /* [avatar] Save image after db update */
        if($input['avatar'] ?? false) {
            $img = Image::make($image_file->path());
            $img->fit(60,60);
            $img->encode('webp',95);
            Storage::disk('avatars')->put($image_name, $img);
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'description' => $input['description'],
            'avatar' => $input['avatar'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
