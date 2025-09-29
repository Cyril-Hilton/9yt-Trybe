import { Component, OnInit } from '@angular/core';
import { Event, EventService } from 'src/app/services/event.service';

@Component({
  selector: 'app-events',
  templateUrl: './events.component.html',
  styleUrls: ['./events.component.scss']
})
export class EventsComponent implements OnInit {
  upcomingEvents: Event[] = [];
  previousEvents: Event[] = [];
  selectedEvents: Event[] = [];

  constructor(private eventService: EventService) { }

  ngOnInit(): void {
    this.upcomingEvents = this.eventService.getUpcomingEvents();
    this.previousEvents = this.eventService.getPreviousEvents();
  }

  onDateSelected(date: Date): void {
    this.selectedEvents = this.eventService.getEventsByDate(date.toISOString().substring(0, 10));
  }
}