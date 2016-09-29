<?php

namespace OSM\ComCollectBundle\Utils;

class MailinglistSource
{    
    private $mailbox;
    
    //
    public function __construct() {

        #TODO: check if this class is neccessary, attachments nerven
        #TODO: put the mailbox data in config
        $this->mailbox = new \PhpImap\Mailbox('{posteo.de:143}INBOX', 'osm.mailcollect@posteo.net', 'gWfzf3LwkdgYlcGZUXOqJDL0g', __DIR__);
    }
    
    public function getNew($maxNew) {
        
        $mails = $this->getMailDatas($maxNew);
        
        return $mails;
    }

    public function setMailsAsRead($mailIds) {
        
        foreach ($mailIds as $mailId) {
            
            $this->mailbox->markMailAsRead($mailId);
        }
    }
    
    private function getNewMailIds() {
        
        $mailIds = $this->mailbox->searchMailbox('UNSEEN');
        
        if (!$mailIds) {
            
            return [];
        }
        
        return $mailIds;
    }
    
    private function getMailDatas($maxNew) {
        
        $mailIds = $this->getNewMailIds();
        
        //get mails from server
        $mails = [];
        foreach ($mailIds as $key => $mailId) {
            
            $mails[$key] = $this->getMailData($mailId);
            
            //stop collecting after given count
            if ($maxNew-- == 0) {
                
                 break;
            }
        }
        
        return $mails;
    }
    
    private function getMailData($mailId) {
        
        //get mail data from Mailbox, dont mark as read
        $mail = $this->mailbox->getMail($mailId, false);
        $mailInfo = $this->mailbox->getMailsInfo([$mailId]);
        
        //create a data array
        $data['mailId'] = $mailId;
        $data['subject'] = $mail->subject;
        $data['textPlain'] = $mail->textPlain;
        $data['fromName'] = $mail->fromName;
        $data['fromAddress'] = $mail->fromAddress;
        $data['date'] = $mail->date;
        $data['messageId'] = $mail->messageId;
        
        $data['inreplytoId'] = $this->getInreplytoId($mailInfo);     
        $data['title'] = $this->getTitleBySubject($data['subject']);
        $data['slug'] = $this->getSlugBySubject($data['subject']);
        
        return $data;
    }
    
    private function getTitleBySubject($subject) {
        
        preg_match('!.*\[.*\](.*)!u', $subject, $title);
        
        if (empty($title)) {
            
            return NULL;
        }
        
        return trim($title[1]);
    }
    
    private function getSlugBySubject($subject) {
        
        preg_match('!\[(.*)\]!', $subject, $slug);
        
        if (empty($slug)) {
            
            return NULL;
        }
        
        return trim($slug[1]);
    }
    
    private function getInreplytoId($mailInfo) {
        
        return isset($mailInfo[0]->in_reply_to) ? $mailInfo[0]->in_reply_to : NULL;
    }
}
