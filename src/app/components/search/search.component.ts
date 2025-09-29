import { Component, EventEmitter, Input, Output } from '@angular/core';
import { GeminiService, SearchResult } from '../../services/gemini.service';

@Component({
  selector: 'app-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.scss']
})
export class SearchComponent {
  @Input() isVisible = false;
  @Output() close = new EventEmitter<void>();

  searchQuery = '';
  searchResults: SearchResult[] = [];

  constructor(private geminiService: GeminiService) { }

  onSearch() {
    if (this.searchQuery.trim()) {
      this.geminiService.searchSite(this.searchQuery).subscribe(results => {
        this.searchResults = results;
      });
    }
  }

  closeSearch() {
    this.close.emit();
    this.searchResults = [];
    this.searchQuery = '';
  }
}