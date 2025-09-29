import { Component, OnInit } from '@angular/core';

interface GalleryEvent {
  id: 'area51' | 'code51' | 'gamesparty';
  name: string;
  images: string[];
}

@Component({
  selector: 'app-gallery',
  templateUrl: './gallery.component.html',
  styleUrls: ['./gallery.component.scss'],
  // Removed standalone: true and imports: [CommonModule] for module-based Angular 15
})
export class GalleryComponent implements OnInit {
  // Static array containing the user-specified local image paths (Increased count for clear pagination)
  private readonly LOCAL_IMAGES: string[] = [
    // 19 original images
    'assets/gallery/code 51/magazine image 1.jpg', 
    'assets/gallery/code 51/magazine image 2.jpg', 
    'assets/gallery/code 51/magazine image 3.jpg', 
    'assets/gallery/code 51/magazine image 4.jpg', 
    'assets/gallery/code 51/magazine image 5.jpg', 
    'assets/gallery/code 51/magazine image 6.jpg', 
    'assets/gallery/code 51/magazine image 1.jpg', 
    'assets/gallery/code 51/magazine image 2.jpg', 
    'assets/gallery/code 51/magazine image 3.jpg', 
    'assets/gallery/code 51/magazine image 4.jpg', 
    'assets/gallery/code 51/magazine image 5.jpg', 
    'assets/gallery/code 51/magazine image 6.jpg', 
    'assets/gallery/code 51/magazine image 6.jpg'
  ];

  // Events data now using the local image paths
  events: GalleryEvent[] = [
    { 
      id: 'area51', 
      name: 'AREA 51', 
      images: this.LOCAL_IMAGES
    },
    { 
      id: 'code51', 
      name: 'CODE 51', 
      images: this.LOCAL_IMAGES
    },
    { 
      id: 'gamesparty', 
      name: 'Games and Party', 
      images: this.LOCAL_IMAGES
    }
  ];

  // State Management (Properties)
  activeEventId: GalleryEvent['id'] = 'area51';
  currentPage: number = 1;
  // Using 6 items per page as defined in the user's selected code snippet
  readonly itemsPerPage = 6;

  // Getter for the currently selected event object
  get activeEvent(): GalleryEvent | undefined {
    return this.events.find(e => e.id === this.activeEventId);
  }

  // Getter for the total number of pages
  get totalPages(): number {
    return Math.ceil((this.activeEvent?.images.length || 0) / this.itemsPerPage);
  }

  // Getter to slice the images for the current page
  get paginatedImages(): string[] {
    const images = this.activeEvent?.images || [];
    const start = (this.currentPage - 1) * this.itemsPerPage;
    const end = start + this.itemsPerPage;
    return images.slice(start, end);
  }

  ngOnInit(): void {
    // Initialization logic
  }
  
  // Track function for *ngFor to optimize rendering (best practice)
  trackById(index: number, event: GalleryEvent): string {
    return event.id;
  }

  // Event handler for tab selection
  setActiveEvent(id: GalleryEvent['id']): void {
    this.activeEventId = id;
    this.currentPage = 1; // Reset page to 1 when switching events
  }

  // Pagination navigation
  nextPage(): void {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
    }
  }

  prevPage(): void {
    if (this.currentPage > 1) {
      this.currentPage--;
    }
  }
}
