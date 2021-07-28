<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriberCreateRequest;
use App\Http\Requests\SubscriberRequest;
use App\Libraries\Utils;
use Http\Client\Exception\HttpException;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SubscriberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(SubscriberRequest $request)
    {
        $by = '';
        $order = 'asc';
        $searchQuery = null;

        //Order functionality
        if ((int) $request->order[0]['column'] === 0) $by = 'email';
        elseif ((int) $request->order[0]['column'] === 1) $by = 'name';

        if ($request->order[0]['dir']) $order = $request->order[0]['dir'];
        if ($request->search['value']) $searchQuery = $request->search['value'];

        $apiKey = Utils::getApiKey($request->sessionId);

        try {
            $subscribersApi = (new \MailerLiteApi\MailerLite($apiKey))->subscribers();

            if ($by && $order) {
                $subscribersApi = $subscribersApi->orderBy($by, $order);
            }

            if (isset($request->length) && isset($request->start)) {
                $subscribersApi = $subscribersApi
                    ->offset($request->start)
                    ->limit($request->length);
            }

            $objectQuery = clone $subscribersApi;

            if ($searchQuery) {
                $data = $subscribersApi->search($searchQuery);
            } else {
                $data = $subscribersApi->get()->toArray();
            }

            if (isset($data[0]->error)) {
                $errorCode = $data[0]->error->code;
                if ($data[0]->error->code === 1) $errorCode = 401;
                elseif ($data[0]->error->code === 2) $errorCode = 404;
                return response()->json(['status' => false, 'message' => $data[0]->error->message], $errorCode)->setCallback($request->input('callback'));
            }

            if ($data) {
                foreach ($data as $item) {
                    $item->id = (string) $item->id;
                }
            }

            $responseData["draw"] = $request->draw;
            $responseData["recordsTotal"] = $objectQuery->count()->count ?? 0;
            $responseData["recordsFiltered"] = $objectQuery->count()->count ?? 0;
            $responseData['data'] = $data ?? [];

            return response()->json($responseData, 200)->setCallback($request->input('callback'));

        }catch (HttpException $e){
            return response()->json($e->getMessage(), $e->getStatusCode())->setCallback($request->input('callback'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  SubscriberCreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SubscriberCreateRequest $request)
    {
        try {
            $apiKey = Utils::getApiKey($request->sessionId);
            $subscribersApi = (new \MailerLiteApi\MailerLite($apiKey))->subscribers();
            $foundSubscriber = $subscribersApi->find($request->email);

            if (isset($foundSubscriber->id)) {
                return response()->json(['status' => false, 'message' => 'Subscriber Already Exists!'], 400)->setCallback($request->input('callback'));
            }

            $newSubscriber = [
                'email' => $request->email,
                'name' => $request->name ? $request->name : '',
                'fields' => [
                    'country' => $request->country ? $request->country : ''
                ]
            ];

            $addedSubscriber = $subscribersApi->create($newSubscriber);

            if (isset($addedSubscriber->error)) {
                return response()->json(['status' => false, 'message' => $addedSubscriber->error->message], $addedSubscriber->error->code)->setCallback($request->input('callback'));
            } elseif ($addedSubscriber) {
                return response()->json(['status' => true, 'message' => 'Subscriber Created Successfully!'], 200)->setCallback($request->input('callback'));
            }

            return response()->json(['status' => false, 'message' => 'Something went wrong!'], 500)->setCallback($request->input('callback'));
        }catch (HttpException $e){
            return response()->json(['status' => false, 'message' => $e->getMessage()], $e->getStatusCode())->setCallback($request->input('callback'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $apiKey = Utils::getApiKey($request->sessionId);
            $subscribersApi = (new \MailerLiteApi\MailerLite($apiKey))->subscribers();

            $subscriberData = [
                'fields' => [
                    'name' => $request->name,
                    'country' => $request->country
                ]
            ];

            $editedSubscriber = $subscribersApi->update($id, $subscriberData);

            if (isset($editedSubscriber->error)) {
                return response()->json(['status' => false, 'message' => $editedSubscriber->error->message], $editedSubscriber->error->code)->setCallback($request->input('callback'));
            } elseif ($editedSubscriber) {
                return response()->json(['status' => true, 'message' => 'Subscriber Edited Successfully!'], 200)->setCallback($request->input('callback'));
            }

            return response()->json(['status' => false, 'message' => 'Something went wrong!'], 500)->setCallback($request->input('callback'));
        }catch (HttpException $e){
            return response()->json(['status' => false, 'message' => $e->getMessage()], $e->getStatusCode())->setCallback($request->input('callback'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @param  SubscriberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, SubscriberRequest $request)
    {
        try {
            $apiKey = Utils::getApiKey($request->sessionId);
            $subscribersApi = (new \MailerLiteApi\MailerLite($apiKey))->subscribers();
            $deletedSubscriber = $subscribersApi->delete($id);

            if (isset($deletedSubscriber->error)) {
                return response()->json(['status' => false, 'message' => $deletedSubscriber->error->message], $deletedSubscriber->error->code)->setCallback($request->input('callback'));
            } elseif ($deletedSubscriber === null) {
                return response()->json(['status' => true, 'message' => 'Subscriber Deleted Successfully!'], 200)->setCallback($request->input('callback'));
            }

            return response()->json(['status' => false, 'message' => 'Something went wrong!'], 400)->setCallback($request->input('callback'));
        } catch (HttpException $e){
            return response()->json(['status' => false, 'message' => $e->getMessage()], $e->getStatusCode())->setCallback($request->input('callback'));
       }
    }
}
