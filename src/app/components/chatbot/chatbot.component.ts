import { Component, EventEmitter, Input, Output } from '@angular/core';
import { GeminiService, ChatMessage } from '../../services/gemini.service';

@Component({
  selector: 'app-chatbot',
  templateUrl: './chatbot.component.html',
  styleUrls: ['./chatbot.component.scss']
})
export class ChatbotComponent {
  @Input() isVisible = false;
  @Output() close = new EventEmitter<void>();

  messages: ChatMessage[] = [];
  currentMessage = '';

  constructor(private geminiService: GeminiService) {
    // Initial bot message
    this.messages.push({ text: 'Hello! How can I help you?', isUser: false });
  }

  sendMessage() {
    if (!this.currentMessage.trim()) return;

    this.messages.push({ text: this.currentMessage, isUser: true });
    
    this.geminiService.sendChatMessage(this.currentMessage).subscribe(response => {
      this.messages.push(response);
    });

    this.currentMessage = '';
  }

  closeChat() {
    this.close.emit();
  }
}