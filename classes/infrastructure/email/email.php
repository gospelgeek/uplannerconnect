<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\email;

use coding_exception;

/**
 * Class email, send email with uPlaner info
 */
class email
{
    /**
     * Send email with uPlanner info
     *
     * @param $recipient_email
     * @param $subject
     * @param $current_date
     * @param $attachment_path
     * @param $attachment_name
     * @return bool
     */
    public function send(
        $recipient_email,
        $subject,
        $current_date,
        $attachment_path,
        $attachment_name
    ): bool {
        try {
            $user = new \stdClass();
            $user->email = $recipient_email;
            $user->id = '000001';
            $user->username = 'univalle';
            $admin = get_admin();
            $subject = get_string($subject, 'local_uplannerconnect');

            $body = 'Dear administrator,

            We are attaching information regarding the changes towards uPlanner.
    
            Best regards,
            Univalle';

            return email_to_user(
                $user,
                $admin,
                $subject . ' - ' . $current_date,
                $body,
                '',
                $attachment_path,
                $attachment_name
            );
        } catch (coding_exception $e) {
            error_log('send: '. $e->getMessage(). "\n");
        }

        return false;
    }
}