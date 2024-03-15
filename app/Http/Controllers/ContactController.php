<?php
namespace App\Http\Controllers;
use App\Models\Contact;
use App\Models\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreContactRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'full_name' => 'required|string',
            'email' => 'required|email|unique:contacts',
            'phone' => 'nullable',
            'message' => 'required|string',
        ];

        $messages = [
            'full_name.required' => 'The full name field is required.',
            'full_name.string' => 'The full name must be a string.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'message.required' => 'The message field is required.',
            'message.string' => 'The message must be a string.',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contact = Contact::create($request->all());




        $mailConfig = MailConfig::first();
        $admin_email = $mailConfig->admin_email??'brhandev2022@gmail.com';

        if($mailConfig->mailerName && $mailConfig->host && $mailConfig->driver && $mailConfig->port && $mailConfig->username && $mailConfig->email_id && $mailConfig->encryption && $mailConfig->password){
            Mail::to('berhanukebito05@gmail.com')->send(new ContactFormMail($contact));
        }

        return response()->json($contact, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        return response()->json($contact);
    }

    /**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \App\Models\Contact  $contact
 * @return \Illuminate\Http\Response
 */
    public function update(Request $request, Contact $contact)
    {
        $rules = [
            'full_name' => 'required|string',
            'email' => 'required|email|unique:contacts,email,' . $contact->id,
            'phone' => 'nullable',
            'message' => 'required|string',
        ];

        $messages = [
            'full_name.required' => 'The full name field is required.',
            'full_name.string' => 'The full name must be a string.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'message.required' => 'The message field is required.',
            'message.string' => 'The message must be a string.',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contact->update($request->all());

        return response()->json($contact, 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(null, 204);
    }
}
