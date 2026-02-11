WITH mails AS (
         SELECT c.id AS contact_id,
            mail.id AS mail_id
           FROM contact c
             JOIN unicaen_mail_mail mail ON c.mail::text ~~ (('%'::text || mail.destinataires) || '%'::text)
        UNION
         SELECT c.id,
            mail.id
           FROM contact c
             JOIN unicaen_mail_mail mail ON c.mail::text ~~ (('%'::text || mail.destinataires_initials) || '%'::text)
        )
 SELECT DISTINCT mails.contact_id,
    mails.mail_id
   FROM mails