import { Component, EventEmitter, Output } from '@angular/core';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent {
  @Output() searchOpened = new EventEmitter<void>();
  @Output() chatOpened = new EventEmitter<void>();

  openSearch() {
    this.searchOpened.emit();
  }

  openChat() {
    this.chatOpened.emit();
  }
}