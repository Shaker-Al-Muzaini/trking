<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;

class ContactController
{
    public function index()
    {
        return inertia('Contact');
    }

    public function store(ContactRequest $request)
    {

        Contact::create($request->all());

        return redirect()->route('contact')
            ->with('success', 'Your message has been sent successfully!');
    }

    public function messages()
    {
        $messages = Contact::latest()->get();

        return inertia('ContactMessages', [
            'messages' => $messages,
        ]);
    }
}
