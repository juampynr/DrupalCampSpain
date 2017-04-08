<?php

namespace Drupal\dcamp_attendees\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\dcamp_attendees\Entity\Attendee;
use Google_Client;
use Google_Service_Sheets;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DcampAttendeesController extends ControllerBase {

  /**
   * The amount of seconds to cache attendee listings.
   *
   * @var int
   */
  protected $maxAge = 240;

  /**
   * Lists attendees.
   *
   * @return mixed
   *   JsonResponse when requested via API request. A render array
   *   otherwise.
   */
  public function listAttendees() {
    $attendees = \Drupal::service('dcamp_attendees.eventbrite')->getAttendees();

    // Check if this is an API request.
    if (\Drupal::request()->query->get('_format') == 'json') {
      // Prepare and send response.
      $headers = [
        'max-age' => $this->maxAge,
      ];
      return new JsonResponse($attendees, Response::HTTP_OK, $headers);
    }

    return [
      '#theme' => 'attendees_list',
      '#attendees' => $attendees,
      '#cache' => [
        'max-age' => $this->maxAge,
      ],
    ];
  }

  /**
   * Shows an attendee profile.
   *
   * @return mixed
   *   JsonResponse when requested via API request. A render array
   *   otherwise.
   */
  public function viewAttendee(Attendee $attendee) {
    // Check if this is an API request.
    if (\Drupal::request()->query->get('_format') == 'json') {
      // Prepare and send response.
      $headers = [
        'max-age' => $this->maxAge,
      ];
      return new JsonResponse($attendee, Response::HTTP_OK, $headers);
    }

    return [
      '#theme' => 'attendees_view',
      '#attendee' => $attendee,
      '#cache' => [
        'max-age' => $this->maxAge,
      ],
    ];
  }

}
