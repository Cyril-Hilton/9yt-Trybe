Hello Admin,

You have received a new chat message on 9yt !Trybe.

From: {{ $chatMessage->sender_name }}
Email: {{ $chatMessage->sender_email }}

Message:
{{ $chatMessage->message }}

---

Please log in to the admin dashboard to reply to this message:
{{ config('app.url') }}/admin/chat

Sent at: {{ $chatMessage->created_at->format('F d, Y h:i A') }}

---
9yt !Trybe
{{ config('app.url') }}
