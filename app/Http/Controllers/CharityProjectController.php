<?php

namespace App\Http\Controllers;

use App\Http\Requests\CharityProjectRequest;
use App\Http\Requests\DonationRequest;
use App\Http\Resources\CharityProjectResource;
use App\Http\Resources\DonationResource;
use App\Models\CharityProject;
use App\Models\Donation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Info(
 *     title="Charity Project API",
 *     version="1.0.0",
 *     description="This is the API documentation for managing charity projects and organizations.",
 *     @OA\Contact(
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Schema(
 *      schema="CharityProject",
 *      type="object",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Charity Project Name"),
 *      @OA\Property(property="status", type="string", example="active"),
 *      @OA\Property(property="launch_date", type="string", format="date", example="2023-01-01"),
 *      @OA\Property(property="description", type="string", example="Description of the charity project"),
 *  )
 *
 * @OA\Schema(
 *      schema="DonationResource",
 *      type="object",
 *      @OA\Property(property="id", type="integer", example=1, description="ID пожертвования"),
 *      @OA\Property(property="charity_project_id", type="integer", example=1, description="ID проекта благотворительности"),
 *      @OA\Property(property="amount", type="number", format="float", example=100.00, description="Сумма пожертвования"),
 *      @OA\Property(property="donation_date", type="string", format="date-time", example="2023-10-01T12:00:00Z", description="Дата пожертвования"),
 *      @OA\Property(property="comment", type="string", example="Спасибо за вашу поддержку!", description="Комментарий к пожертвованию")
 *  )
 */


class CharityProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/charity-projects",
     *     tags={"Charity Projects"},
     *     summary="Get a list of charity projects",
     *     description="Retrieves a list of charity projects with optional filters.",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by project status (active, closed)",
     *         @OA\Schema(type="string", enum={"active", "closed"})
     *     ),
     *     @OA\Parameter(
     *         name="launch_date",
     *         in="query",
     *         required=false,
     *         description="Filter by launch date (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page (between 3 and 10)",
     *         @OA\Schema(type="integer", minimum=3, maximum=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of charity projects",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CharityProject")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No charity projects found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     * )
     */
    public function index(CharityProjectRequest $request): JsonResponse|AnonymousResourceCollection
    {
        // Получаем параметры фильтрации и пагинации
        $status = $request->input('status', 'active'); // По умолчанию 'active'
        $launchDate = $request->input('launch_date'); // Дата запуска
        $perPage = min($request->input('per_page', 3), 10); // Число элементов на странице, максимум 10

        // Фильтрация проектов
        $query = CharityProject::query();

        // Фильтрация по статусу
        if ($status === 'closed') {
            $query->where('status', 'closed');
        } else {
            $query->where('status', 'active');
        }

        // Фильтрация по дате запуска, если указана
        if ($launchDate) {
            $query->whereDate('launch_date', $launchDate);
        }

        // Сортировка
        $query->orderBy('sort_order', 'asc')
            ->orderBy('launch_date', 'desc');

        // Пагинация
        $projects = $query->paginate($perPage);

        if ($projects->isEmpty()) {
            return CharityProjectResource::emptyResponse();
        }

        return CharityProjectResource::collection($projects);
    }

    /**
     * @OA\Get(
     *     path="/v1/charity-projects/{slug}",
     *     tags={"Charity Projects"},
     *     summary="Get a charity project by slug",
     *     description="Retrieves a charity project by its slug.",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the charity project",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A charity project",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Проект 1"),
     *             @OA\Property(property="slug", type="string", example="proekt-1"),
     *             @OA\Property(property="short_description", type="string", example="<p>Краткое описание проекта 1.</p>"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="launch_date", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="donation_amount", type="integer", example=1000),
     *             @OA\Property(property="additional_description", type="string", example="<p>Дополнительное описание проекта 1.</p>")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No project found"
     *     )
     * )
     */
    public function show(string $slug): JsonResponse|CharityProjectResource
    {
        $project = CharityProject::where('slug', $slug)->first();

        if (!$project) {
            return CharityProjectResource::emptyResponse();
        }

        return new CharityProjectResource($project, true);
    }


    /**
     * @OA\Post(
     *     path="/v1/donate",
     *     summary="Создать пожертвование",
     *     tags={"Donations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"charity_project_id", "amount"},
     *             @OA\Property(property="charity_project_id", type="integer", example=1, description="ID проекта благотворительности"),
     *             @OA\Property(property="amount", type="number", format="float", example=100.00, description="Сумма пожертвования"),
     *             @OA\Property(property="donation_date", type="string", format="date-time", example="2023-10-01T12:00:00Z", description="Дата пожертвования (по умолчанию текущее время)"),
     *             @OA\Property(property="comment", type="string", example="Спасибо за вашу поддержку!", description="Комментарий к пожертвованию")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Donation successfully created",
     *         @OA\JsonContent(ref="#/components/schemas/DonationResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No project found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No organizations found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create donation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to create donation"),
     *             @OA\Property(property="error", type="string", example="Error message here")
     *         )
     *     )
     * )
     */
    public function donate(DonationRequest $request): DonationResource|JsonResponse
    {
        // Получаем данные из запроса
        $charityProjectId = $request->input('charity_project_id');
        $amount = $request->input('amount');
        $donationDate = $request->input('donation_date', now());
        $comment = $request->input('comment');

        try {
            // Создаем новое пожертвование
            $donation = Donation::create([
                'charity_project_id' => $charityProjectId,
                'amount' => $amount,
                'donation_date' => $donationDate,
                'comment' => $comment,
            ]);
        } catch (\Exception $exception) {
            return DonationResource::failedResponse($exception);
        }

        // Обновляем сумму пожертвований проекта
        $charityProject = CharityProject::find($charityProjectId);
        if (!$charityProject) {
            return DonationResource::emptyResponse();
        }
        $charityProject->donation_amount = $charityProject->donations()->sum('amount');
        $charityProject->save();

        return new DonationResource($donation);
    }

}
