<?php

namespace Despark\LaravelDbLocalization;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;

class ContactsController extends BaseController
{
    /**
     * Display a listing of contact.
     *
     * @return Response
     */
    public function index()
    {
        $contacts = Contacts::all();

        return View::make('laravel-db-localization::contacts.index', ['contacts' => $contacts]);
    }

    /**
     * Show the form for creating a new contact.
     *
     * @return Response
     */
    public function create()
    {
        $i18ns = I18n::all();

        return View::make('laravel-db-localization::contacts.create', ['i18ns' => $i18ns]);
    }

    /**
     * Store a newly created contact in storage.
     *
     * @return Response
     */
    public function store()
    {
        $contact = new Contacts();
        $contact->create(Input::all());

        return Redirect::route('localization_example.index');
    }

    /**
     * Show contact.
     *
     * @return Response
     */
    public function show()
    {
        return Redirect::route('localization_example.index');
    }

    /**
     * Show the form for editing the specified contact.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $contact = Contacts::findOrFail($id);
        $i18ns = I18n::all();

        return View::make('laravel-db-localization::contacts.edit', ['i18ns' => $i18ns, 'contact' => $contact]);
    }

    /**
     * Update the specified contact in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update($id)
    {
        $contact = Contacts::findOrFail($id);
        $contact->save(Input::all());

        return Redirect::route('localization_example.index');
    }
    /**
     * Remove the specified contact from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        Contacts::findOrFail($id)->delete();

        return Redirect::route('localization_example.index');
    }
}
