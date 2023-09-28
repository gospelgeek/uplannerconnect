<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\email;

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Send email with Upplaner info
 */
class email
{
    /**
     * Send email with Uplanner info
     *
     * @return bool
     * @throws \coding_exception
     */
    public function send($recipient_email, $attachment_path)
    {
        $filename = basename($attachment_path);
        $user = new \stdClass();
        $user->email = $recipient_email;
        $admin = get_admin();
        
        $subject = get_string('upllaner_email_subject', 'local_uplannerconnect');
    
        $body = 'Dear administrator,

        We are attaching information regarding the changes towards uPlanner.

        Best regards,
        Univalle';
    
        return email_to_user(
            $admin,
            $user,
            $subject,
            $body,
            '',
            $attachment_path,
            $filename
        );
    }
}