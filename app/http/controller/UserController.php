<?php

namespace app\http\controller;

use lib\http\Request;
use lib\http\Response;

class UserController
{
    public static function index(Request $request): Response
    {
        // Get all data from the model

        // TODO

        return Response::withMessage('Not implemented yet', 501);
    }

    public static function show(Request $request): Response
    {
        // Get data from the model by id
        $validated = $request->validate([
            'id' => 'required|integer'
        ]);

        $id = $validated['id'];

        // TODO

        return Response::withMessage('Not implemented yet', 501);
    }

    public static function store(Request $request): Response
    {
        // Store data to the model
        $validated = $request->validate([
            // Add validation rules here
        ]);

        // TODO

        return Response::withMessage('Not implemented yet', 501);
    }

    public static function update(Request $request): Response
    {
        // Update data to the model
        $validated = $request->validate([
            'id' => 'required|integer'
            // Add validation rules here
        ]);

        $id = $validated['id'];

        // TODO

        return Response::withMessage('Not implemented yet', 501);
    }

    public static function delete(Request $request): Response
    {
        // Delete data from the model
        $validated = $request->validate([
            'id' => 'required|integer'
        ]);

        $id = $validated['id'];

        // TODO

        return Response::withMessage('Not implemented yet', 501);
    }
}
