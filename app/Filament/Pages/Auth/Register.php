<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Register extends BaseRegister
{
    protected function handleRegistration(array $data): Model
    {
        // Önce kullanıcının standart kaydını (User tablosuna eklenmesini) sağlıyoruz
        $user = parent::handleRegistration($data);

        // Kullanıcı başarıyla oluşturulduktan sonra, ID'si 2 olan rolü buluyoruz
        $role = Role::find(2);

        // Eğer öyle bir rol veritabanında varsa, kullanıcıya atıyoruz
        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }
}
