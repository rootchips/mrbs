<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        $logo = sprintf('%s/%s', config('app.web'), 'images/logos/logo.png');
        $website = config('app.web');

        $template = new Template;
        $template->type = 'Email';
        $template->name = 'staff-email-verification';
        $template->subject = 'Verify Your Account with Your Verification Code';
        $html = "<div style='background-color: #f4f4f4; padding: 20px; font-family: Arial, Helvetica, sans-serif;'>";
        $html .= "<table style='background-color: #ffffff; padding: 20px; max-width: 600px; margin: 0 auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);' cellpadding='0' cellspacing='0' border='0'>";
        $html .= "<tr><td style='text-align: center; padding-bottom: 20px;'>";
        $html .= "<img src='{$logo}' style='max-width: 150px;' />";
        $html .= "</td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Hi, {{ name }}";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Welcome to Meeting Room Booking System! We’re excited to have you on board. Please use the verification code below to verify your account.";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='text-align: center;'>";
        $html .= "<div style='margin: 30px 0; padding: 10px 0; text-align: center; background-color: #f9f9f9; border-radius: 8px; font-size: 36px; font-weight: bold; letter-spacing: 10px; color: #333;'>";
        $html .= '{{ verification_code }}';
        $html .= '</div>';
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 20px 0;'></td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Need help? Contact us at roslansaidi@ymail.com / +6017 2468180.";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Thank you,<br>";
        $html .= "Meeting Room Booking System";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 14px; color: #777; text-align: left;'>";
        $html .= "<a href='{$website}' style='color: #007bff;'>{$website}</a>";
        $html .= "</td></tr>";
        $html .= "</table></div>";
        $template->content = $html;
        $template->description = 'Account verification with verification code for staff.';
        $template->parameter = json_encode(["{{ name }}", "{{ verification_code }}"], true);
        $template->save();

        $template = new Template;
        $template->type = 'Email';
        $template->name = 'internal-email-activation';
        $template->subject = 'Verify Your Account and Set Up Your Password';
        $html = "<div style='background-color: #f4f4f4; padding: 20px; font-family: Arial, Helvetica, sans-serif;'>";
        $html .= "<table style='background-color: #ffffff; padding: 20px; max-width: 600px; margin: 0 auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);' cellpadding='0' cellspacing='0' border='0'>";
        $html .= "<tr><td style='text-align: center; padding-bottom: 20px;'>";
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" style="height: 2.5rem; width: auto; color: #7f1d1d;">';
        $html .= '<rect width="70" height="70" x="15" y="15" fill="none" stroke="currentColor" stroke-width="5" rx="10" />';
         $html .= '<circle cx="30" cy="10" r="5" fill="currentColor" />';
         $html .= '<circle cx="70" cy="10" r="5" fill="currentColor" />';
         $html .= '<path stroke="currentColor" stroke-width="3" d="M25 40h50M25 55h50M25 70h50M40 50h20" />';
         $html .= '<path fill="currentColor" d="M35 55h5v10h-5zM60 55h5v10h-5z" />';
         $html .= '</svg>';
        $html .= "</td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Hi, {{ name }}";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Welcome to Meeting Room Booking System! We’re excited to have you on board. Please verify your account and set up your password.";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 18px; color: #333; text-align: left;'>Your Account Details:</td></tr>";
        $html .= "<tr><td style='padding: 5px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Email: <b>{{ email }}</b><br>";
        $html .= "Temporary Password: <b>{{ password }}</b>";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 20px 0;'></td></tr>";
        $html .= "<tr><td style='text-align: left;'>";
        $html .= "<a href='{{ link }}' style='display: inline-block; padding: 10px 20px; background-color: #800000; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
        $html .= "Verify My Account";
        $html .= "</a></td></tr>";
        $html .= "<tr><td style='padding: 20px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Once you verify your account and login into the system, you’ll be prompted to change your temporary password for security.";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Need help? Contact us at roslansaidi@ymail.com / +6017 2468180.";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Thank you,<br>";
        $html .= "Meeting Room Booking System";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 14px; color: #777; text-align: left;'>";
        $html .= "<a href='{$website}' style='color: #007bff;'>{$website}</a>";
        $html .= "</td></tr>";
        $html .= "</table></div>";
        $template->content = $html;
        $template->description = 'Account verification and password setup for new users.';
        $template->parameter = json_encode(["{{ name }}", "{{ email }}", "{{ password }}", "{{ link }}"], true);
        $template->save();

        $register = new Template;
        $register->type = 'Email';
        $register->name = 'reset-password';
        $register->subject = 'Reset your password';
        $register->description = 'To reset password for account in the system';
        $html = "<div style='background-color: #f4f4f4; padding: 20px; font-family: Arial, Helvetica, sans-serif;'>";
        $html .= "<table style='background-color: #ffffff; padding: 20px; max-width: 600px; margin: 0 auto; ";
        $html .= "border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);' cellpadding='0' cellspacing='0' border='0'>";
        
        $html .= "<tr><td style='text-align: center; padding-bottom: 20px;'>";
        $html .= "<img src='{$logo}' style='max-width: 150px;' />";
        $html .= "</td></tr>";
        
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Hi, {{ name }}";
        $html .= "</td></tr>";
        
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "We received a request to reset your password for your account. If you didn't request this, please ignore this email. ";
        $html .= "Otherwise, you can reset your password using the link below:";
        $html .= "</td></tr>";
        
        $html .= "<tr><td style='padding: 20px 0;'></td></tr>";
        
        $html .= "<tr><td style='text-align: left;'>";
        $html .= "<a href='{{ link }}' style='display: inline-block; padding: 10px 20px; background-color: #800000; color: #fff; ";
        $html .= "text-decoration: none; border-radius: 5px; font-size: 16px;'>";
        $html .= "Reset My Password";
        $html .= "</a></td></tr>";
        $html .= "<tr><td style='padding: 20px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Please note: This password reset link will expire in 24 hours.";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "If you have any questions or need further assistance, feel free to contact us at roslansaidi@ymail.com / +6017 2468180.";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 16px; color: #333; text-align: left;'>";
        $html .= "Thank you,<br>Meeting Room Booking System";
        $html .= "</td></tr>";
        $html .= "<tr><td style='padding: 10px 0;'></td></tr>";
        $html .= "<tr><td style='font-size: 14px; color: #777; text-align: left;'>";
        $html .= "<a href='{$website}' style='color: #007bff;'>{$website}</a>";
        $html .= "</td></tr>";
        $html .= "</table></div>";

        $register->content = $html;    
        $register->parameter = json_encode(["{{ name }}", "{{ link }}"], true);
        $register->save();
    }
}
