<?php

namespace App\Http\Controllers;

use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ContactRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    private $appointmentRepository;
    private $contactRepository;

    public function __construct(
        AppointmentRepositoryInterface $appointmentRepository,
        ContactRepositoryInterface     $contactRepository
    )
    {
        // Set middleware
        $this->middleware(['auth:api']);
        // Set repositories
        $this->appointmentRepository = $appointmentRepository;
        $this->contactRepository = $contactRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return object
     */
    public function index()
    {
        // Get all data from data provider
        $data = $this->appointmentRepository->allByUser(Auth::user()->id);
        // Setting return status code for request
        $statusCode = count($data) > 0 ? 200 : 204;
        return ApiResponses::send($statusCode, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return object
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'datetime' => 'required|date',
            'contactName' => 'required',
            'contactSurname' => 'required',
            'contactPhone' => 'required|numeric',
            'contactEmail' => 'required|email:rfc,dns'
        ], [
            'required' => 'The :attribute field is required.',
        ]);

        // If validator fails return errors
        if ($validator->fails()) {
            return ApiResponses::send(
                422,
                (object)["errors" => $validator->errors()]
            );
        }

        // If validator passes, do the job
        $contact = $this->contactRepository->create([
            "name" => $request->contactName,
            "surname" => $request->contactSurname,
            "phone" => $request->contactPhone,
            "email" => $request->contactEmail,
        ]);

        $distanceInfo = new AddressInfoController(env('ESTATE_ADDRESS'), $request->address);

        // If contact added successfully, insert appointment
        if ($contact) {
            // Calculate estimated departure datetime
            $estimated_departure = strtotime($request->datetime) - $distanceInfo->duration["value"];

            // Calculating estimated arrival datetime
            $estimated_arrival = $estimated_departure + (($distanceInfo->duration["value"] * 2) + 3600);

            // Setting values for database
            $appointmentData = [
                "address" => $request->address,
                "trip_distance" => $distanceInfo->distance["value"],
                "trip_duration" => $distanceInfo->duration["value"],
                "user_id" => Auth::user()->id,
                "contact_id" => $contact->id,
                "datetime" => $request->datetime,
                "estimated_departure" => date('Y-m-d H:i:s', $estimated_departure),
                "estimated_arrival_to_office" => date('Y-m-d H:i:s', $estimated_arrival),
                "status" => false
            ];
            $newAppointment = $this->appointmentRepository->create($appointmentData);

            if ($newAppointment) {
                return ApiResponses::send(201, $newAppointment);
            }
        } else {
            return ApiResponses::send(402, $request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $appointment
     * @return object
     */
    public function show($appointmentId)
    {
        // Get data by id from data provider
        $data = $this->appointmentRepository->findById($appointmentId);
        if(Auth::user()->id !== $data->user_id) {
            return ApiResponses::send(401, "You don't have access permission.");
        }
        // Setting return status code for request
        $statusCode = $data ? 200 : 204;
        return ApiResponses::send($statusCode, $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $appointmentId
     * @return object
     */
    public function update(Request $request, $appointmentId)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'datetime' => 'required|date',
            'contactName' => 'required',
            'contactSurname' => 'required',
            'contactPhone' => 'required|numeric',
            'contactEmail' => 'required|email:rfc,dns'
        ], [
            'required' => 'The :attribute field is required.',
        ]);

        // If validator fails return errors
        if ($validator->fails()) {
            return ApiResponses::send(
                422,
                (object)["errors" => $validator->errors()]
            );
        }

        // Get appointment by id
        $appointment = $this->appointmentRepository->findById($appointmentId);

        if(Auth::user()->id !== $appointment->user_id) {
            return ApiResponses::send(401, "You don't have access permission.");
        }

        // If validator passes, do the job
        $contact = $this->contactRepository->update($appointment->contact_id, [
            "name" => $request->contactName,
            "surname" => $request->contactSurname,
            "phone" => $request->contactPhone,
            "email" => $request->contactEmail,
        ]);

        $distanceInfo = new AddressInfoController(env('ESTATE_ADDRESS'), $request->address);

        // If contact updated successfully, update appointment
        if ($contact) {
            // Calculate estimated departure datetime
            $estimated_departure = strtotime($request->datetime) - $distanceInfo->duration["value"];

            // Calculating estimated arrival datetime
            $estimated_arrival = $estimated_departure + (($distanceInfo->duration["value"] * 2) + 3600);

            // Setting values for database
            $appointmentData = [
                "address" => $request->address,
                "trip_distance" => $distanceInfo->distance["value"],
                "trip_duration" => $distanceInfo->duration["value"],
                "user_id" => Auth::user()->id,
                "contact_id" => $appointment->contact_id,
                "datetime" => $request->datetime,
                "estimated_departure" => date('Y-m-d H:i:s', $estimated_departure),
                "estimated_arrival_to_office" => date('Y-m-d H:i:s', $estimated_arrival),
                "status" => false
            ];
            $editAppointment = $this->appointmentRepository->update($appointmentId, $appointmentData);

            if ($editAppointment) {
                return ApiResponses::send(200, $this->appointmentRepository->findById($appointmentId));
            }
        } else {
            return ApiResponses::send(402, $request);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $appointmentId
     * @return object
     */
    public function destroy($appointmentId)
    {
        $appointment = $this->appointmentRepository->findById($appointmentId);

        if(Auth::user()->id !== $appointment->user_id) {
            return ApiResponses::send(401, "You don't have access permission.");
        }

        if ($appointment) {
            $deleteContact = $this->contactRepository->deleteById($appointmentId);
            if ($deleteContact) {
                $deleteAppointment = $this->appointmentRepository->deleteById($appointmentId);
                if ($deleteAppointment) {
                    return ApiResponses::send(200, ["Appointment deleted successfully."]);
                } else {
                    return ApiResponses::send(400, ["Appointment couldn't be deleted."]);
                }
            } else {
                return ApiResponses::send(400, ["Contact that related to appointment, couldn't be deleted."]);
            }
        } else {
            return ApiResponses::send(404, ["Appointment couldn't be found."]);
        }
    }
}
