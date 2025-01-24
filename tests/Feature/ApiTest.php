<?php

namespace Tests\Feature;

use App\Models\CharityProject;
use App\Models\Donation;
use Mockery;
use Tests\TestCase;

class ApiTest extends TestCase
{

    public function test_it_returns_a_list_of_charity_projects()
    {
        // Создаем мок для модели CharityProject
        $charityProjectMock = Mockery::mock(CharityProject::class);
        $charityProjectMock->shouldReceive('query->paginate')
            ->andReturn(new \Illuminate\Pagination\LengthAwarePaginator(
                [
                    new CharityProject([
                        'name' => 'Проект 2',
                        'slug' => 'proekt-2',
                        'short_description' => '<p>Краткое описание проекта 2.</p>',
                        'status' => 'active',
                        'launch_date' => '2025-01-17 11:22:00',
                    ]),
                    new CharityProject([
                        'name' => 'Проект 7',
                        'slug' => 'proekt-7',
                        'short_description' => '<p>Краткое описание проекта 7.</p>',
                        'status' => 'active',
                        'launch_date' => '2025-01-12 06:22:00',
                    ]),
                ],
                4,
                3,
                1
            ));

        // Подменяем модель в контейнере
        $this->app->instance(CharityProject::class, $charityProjectMock);

        // Выполняем запрос
        $response = $this->getJson('/api/v1/charity-projects');

        // Проверяем успешный ответ
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'slug',
                        'short_description',
                        'status',
                        'launch_date',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        // Сценарий 2: Ничего не найдено
        $emptyCharityProjectMock = Mockery::mock(CharityProject::class);
        $emptyCharityProjectMock->shouldReceive('query->paginate')
            ->andReturn(new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                3,
                1
            ));

        // Подменяем модель в контейнере
        $this->app->instance(CharityProject::class, $emptyCharityProjectMock);

        // Выполняем запрос с параметрами, которые не вернут результатов
        $response = $this->getJson('/api/v1/charity-projects?status=closed&launch_date=2055-09-22');

        // Проверяем ответ 404 с сообщением
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No project found',
            ]);
    }


    public function test_it_returns_a_charity_project_by_slug()
    {
        // Сценарий 1: Проект найден
        $charityProjectMock = Mockery::mock(CharityProject::class);
        $charityProjectMock->shouldReceive('where->first')
            ->with('slug', 'proekt-1') // Используем реальный slug
            ->andReturn(new CharityProject([
                'id' => 1,
                'name' => 'Проект 1',
                'slug' => 'proekt-1',
                'short_description' => '<p>Краткое описание проекта 1.</p>',
                'status' => 'draft',
                'launch_date' => '2025-01-26 11:21:59',
            ]));

        // Подменяем модель в контейнере
        $this->app->instance(CharityProject::class, $charityProjectMock);

        // Выполняем запрос
        $response = $this->getJson('/api/v1/charity-projects/proekt-1');

        // Проверяем успешный ответ
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Проект 1',
                    'slug' => 'proekt-1',
                    'short_description' => '<p>Краткое описание проекта 1.</p>',
                    'status' => 'draft',
                    'launch_date' => '2025-01-26 11:21:59',
                ],
            ]);

        // Сценарий 2: Проект не найден
        $emptyCharityProjectMock = Mockery::mock(CharityProject::class);
        $emptyCharityProjectMock->shouldReceive('where->first')
            ->with('slug', 'non-existent-slug') // Используем несуществующий slug
            ->andReturnNull();

        // Подменяем модель в контейнере
        $this->app->instance(CharityProject::class, $emptyCharityProjectMock);

        // Выполняем запрос
        $response = $this->getJson('/api/v1/charity-projects/non-existent-slug');

        // Проверяем ответ 404 с сообщением
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No project found',
            ]);
    }


    public function test_it_creates_a_donation_and_updates_project_total()
    {
        // Сценарий 1: Успешное создание пожертвования
        $charityProjectMock = Mockery::mock(CharityProject::class);
        $charityProjectMock->shouldReceive('find')
            ->with(3) // Используем реальный ID проекта
            ->andReturn(new CharityProject([
                'id' => 3,
                'donation_amount' => 0,
            ]));

        // Создаем мок для модели Donation
        $donationMock = Mockery::mock(Donation::class);
        $donationMock->shouldReceive('create')
            ->andReturn(new Donation([
                'id' => 54, // ID может быть любым
                'charity_project_id' => 3,
                'amount' => 1000,
                'donation_date' => '2023-10-01T12:00:00Z',
                'comment' => 'Спасибо за вашу поддержку!',
            ]));

        // Подменяем модели в контейнере
        $this->app->instance(CharityProject::class, $charityProjectMock);
        $this->app->instance(Donation::class, $donationMock);

        // Данные для пожертвования
        $data = [
            'charity_project_id' => 3,
            'amount' => 1000,
            'donation_date' => '2023-10-01T12:00:00Z',
            'comment' => 'Спасибо за вашу поддержку!',
        ];

        // Выполняем запрос
        $response = $this->postJson('/api/v1/donate', $data);

        // Проверяем успешный ответ
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'message' => 'Donation successfully created',
                    'donation' => [
                        'charity_project_id' => 3,
                        'amount' => 1000,
                        'donation_date' => '2023-10-01T12:00:00Z',
                        'comment' => 'Спасибо за вашу поддержку!',
                    ],
                ],
            ]);

        // Сценарий 2: Дата из будущего
        $futureDateData = [
            'charity_project_id' => 3,
            'amount' => 1000,
            'donation_date' => now()->addDay()->toDateTimeString(),
            'comment' => 'Спасибо за вашу поддержку!',
        ];

        $response = $this->postJson('/api/v1/donate', $futureDateData);

        // Проверяем ответ с ошибкой
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The donation date field must be a date before or equal to now.',
                'errors' => [
                    'donation_date' => [
                        'The donation date field must be a date before or equal to now.',
                    ],
                ],
            ]);

        // Сценарий 3: Сумма меньше 0
        $negativeAmountData = [
            'charity_project_id' => 3,
            'amount' => -100,
            'donation_date' => '2023-10-01T12:00:00Z',
            'comment' => 'Спасибо за вашу поддержку!',
        ];

        $response = $this->postJson('/api/v1/donate', $negativeAmountData);

        // Проверяем ответ с ошибкой
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The amount field must be at least 1.',
                'errors' => [
                    'amount' => [
                        'The amount field must be at least 1.',
                    ],
                ],
            ]);

        // Сценарий 4: Неверный charity_project_id
        $invalidProjectIdData = [
            'charity_project_id' => 999, // Несуществующий ID
            'amount' => 1000,
            'donation_date' => '2023-10-01T12:00:00Z',
            'comment' => 'Спасибо за вашу поддержку!',
        ];

        $response = $this->postJson('/api/v1/donate', $invalidProjectIdData);

        // Проверяем ответ с ошибкой
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The selected charity project id is invalid.',
                'errors' => [
                    'charity_project_id' => [
                        'The selected charity project id is invalid.',
                    ],
                ],
            ]);

        // Сценарий 5: Невалидная дата
        $invalidDateData = [
            'charity_project_id' => 3,
            'amount' => 1000,
            'donation_date' => 'invalid-date', // Невалидная дата
            'comment' => 'Спасибо за вашу поддержку!',
        ];

        $response = $this->postJson('/api/v1/donate', $invalidDateData);

        // Проверяем ответ с ошибкой
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The donation date field must be a valid date.',
                'errors' => [
                    'donation_date' => [
                        'The donation date field must be a valid date.',
                    ],
                ],
            ]);
    }

    protected function tearDown(): void
    {
        // Очищаем моки после каждого теста
        Mockery::close();
        parent::tearDown();
    }
}
