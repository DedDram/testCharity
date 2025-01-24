<?php
/**
 * @OA\Info(
 *     title="API",
 *     version="1.0.0",
 *     description="This is the API documentation",
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
 *      schema="Organization",
 *      type="object",
 *      required={"id", "name", "building_id"},
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="building_id", type="integer")
 *  )
 */
