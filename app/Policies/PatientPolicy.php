<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($patient->trashed()) {
            return $user->isAdmin();
        }

        return $this->managesPatient($user, $patient);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Patient $patient): bool
    {
        if ($patient->trashed()) {
            return false;
        }

        return $this->managesPatient($user, $patient);
    }

    public function delete(User $user, Patient $patient): bool
    {
        if ($patient->trashed()) {
            return false;
        }

        return $this->managesPatient($user, $patient);
    }

    /**
     * Pacientes compartidos por centro: cualquier usuario del mismo centro
     * puede verlos y editarlos. Sin centro, solo el dueño.
     */
    private function managesPatient(User $user, Patient $patient): bool
    {
        if ($user->isAdmin() || $patient->user_id === $user->id) {
            return true;
        }

        return $patient->belongsToSameCenter($user);
    }
}
