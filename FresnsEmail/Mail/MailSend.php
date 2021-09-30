<?php


namespace App\Plugins\FresnsEmail\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailSend extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title='fresns title',$content='fresns content')
    {
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->html($this->content)->subject($this->title);
    }
}
