<?php

namespace App\Models\Patient\Services;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Patient Service Interface
 * Defines the contract for patient operations in your module
 */
interface PatientServiceInterface
{
    /**
     * Register a new patient
     */
    public function registerPatient(array $patientData): Patient;
    
    /**
     * Update patient information
     */
    public function updatePatient(Patient $patient, array $updateData): Patient;
    
    /**
     * Get patient profile with medical information
     */
    public function getPatientProfile(int $patientId): Patient;
    
    /**
     * Update patient medical information
     */
    public function updateMedicalInfo(Patient $patient, array $medicalData): Patient;
    
    /**
     * Deactivate patient account
     */
    public function deactivatePatient(Patient $patient): bool;
    
    /**
     * Search patients by criteria
     */
    public function searchPatients(array $criteria): \Illuminate\Database\Eloquent\Collection;
}
