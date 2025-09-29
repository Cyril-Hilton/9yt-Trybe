import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router'; 
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { EventService, Event } from '../../services/event.service'; // Assuming EventService exists
import { GeminiService, NewsItem } from 'src/app/services/gemini.service';
import { Subscription, interval } from 'rxjs'; // Import Subscription and interval

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit, OnDestroy {
  showEventForm = false;
  eventForm: FormGroup;
  upcomingEvents: Event[] = [];

  // --- Carousel Properties ---
  entertainmentNews: NewsItem[] = [];
  currentNewsIndex = 0; // Index of the first visible item
  itemsPerView = 4; // Display 4 items in a row
  private autoAdvanceSubscription: Subscription | undefined;

  // Note: magazineImages array removed as we are using dynamic news data

  constructor(
    private fb: FormBuilder,
    private eventService: EventService,
    private geminiService: GeminiService,
    private router: Router // Inject Router for navigation
  ) {
    this.eventForm = this.fb.group({
      name: ['', Validators.required],
      summary: ['', Validators.required],
      isRecurring: [false],
      date: [''],
      day: [''],
      flyer: ['', Validators.required],
    });
  }

  ngOnInit() {
    // 1. Load Upcoming Events
    this.upcomingEvents = this.eventService.getUpcomingEvents();
    
    // 2. Fetch News and Start Auto-Shuffle
    this.geminiService.fetchEntertainmentNews().subscribe(news => {
      this.entertainmentNews = news;
      this.startAutoAdvance();
    });
  }
  
  // Cleanup subscription when the component is destroyed
  ngOnDestroy() {
    if (this.autoAdvanceSubscription) {
      this.autoAdvanceSubscription.unsubscribe();
    }
  }

  // --- Carousel Logic ---
  startAutoAdvance() {
    // Automatically shuffle the carousel every 8 seconds (8000ms)
    this.autoAdvanceSubscription = interval(8000).subscribe(() => {
      this.nextNews();
    });
  }

  get visibleNews(): NewsItem[] {
    const total = this.entertainmentNews.length;
    if (total === 0) return [];

    let newsSlice: NewsItem[] = [];
    // Calculate the 4 visible items, wrapping around the array if necessary
    for (let i = 0; i < this.itemsPerView; i++) {
      const index = (this.currentNewsIndex + i) % total;
      newsSlice.push(this.entertainmentNews[index]);
    }
    return newsSlice;
  }

  nextNews() {
    const total = this.entertainmentNews.length;
    if (total === 0) return;
    // Advance by one item, wrapping around to the start
    this.currentNewsIndex = (this.currentNewsIndex + 1) % total;
    this.resetAutoAdvance(); 
  }

  prevNews() {
    const total = this.entertainmentNews.length;
    if (total === 0) return;
    // Go back one item, ensuring correct wrap-around calculation
    this.currentNewsIndex = (this.currentNewsIndex - 1 + total) % total;
    this.resetAutoAdvance();
  }

  resetAutoAdvance() {
    // Resets and restarts the auto-advance timer on manual interaction
    if (this.autoAdvanceSubscription) {
      this.autoAdvanceSubscription.unsubscribe();
    }
    this.startAutoAdvance();
  }
  
  // --- Navigation & Form Methods ---
  goToGallery() {
    this.router.navigate(['/gallery']);
  }

  onDateSelected(date: Date) {
    console.log('Events on selected date:', this.eventService.getEventsByDate(date.toISOString().substring(0, 10)));
  }

  publishEvent() {
    if (this.eventForm.valid) {
      this.eventService.addEvent(this.eventForm.value);
      // Using console.log instead of alert()
      console.log('Event submitted for review!'); 
      this.eventForm.reset({ isRecurring: false });
      this.showEventForm = false;
    }
  }
}