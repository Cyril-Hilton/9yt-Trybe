Hello {{ $chatMessage->sender_name }},

You have received a reply from 9yt !Trybe Support!

Your Message:
{{ $chatMessage->message }}

Our Reply:
{{ $chatMessage->admin_reply }}

---

If you have more questions, feel free to visit our website and send us another message.

{{ config('app.url') }}

Thank you for contacting us!

---
9yt !Trybe Support Team
{{ config('app.url') }}
