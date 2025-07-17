<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Src\admisiones\domain\Programa;
use Src\shared\di\FabricaDeRepositorios;

/**
 * Class User
 * 
 * @property string $role
 * @method void pantallaDeInicio()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'prog_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Personaliza la notificación de restablecimiento de contraseña.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Pantalla de inicio de acuerdo al rol
     */
    public function pantallaDeInicio(): string {
        if ($this->role == "Admin") {
            return "procesos.index";
        }

        if ($this->role == "ProgramaAcademico") {
            return 'programa_academico.dashboard';
        }

        return "dashboard";
    }


    /**
     * Retorna el programa académico asignado al usuario.
     *
     * @return Programa
     */
    public function programaAcademico(): Programa
    {
        $programaRepo = FabricaDeRepositorios::getInstance()->getProgramaRepository();

        return $programaRepo->buscarPorID($this->prog_id);
    }
}
