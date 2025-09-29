import { Injectable } from '@angular/core';

export interface Event {
  id: number;
  name: string;
  summary: string;
  date: string;
  isRecurring?: boolean;
  day?: string;
  flyer: string;
}

@Injectable({
  providedIn: 'root'
})
export class EventService {
  private events: Event[] = [
    { id: 1, name: 'Code  51', summary: 'The best Friday night party!', date: '2025-10-29', isRecurring: true, day: 'Friday', flyer: 'assets/upcoming events images/upcoming event image 1.png' },
    { id: 2, name: 'Code  51', summary: 'A party with deep night energy.', date: '2025-10-10', flyer: 'assets/upcoming events images/upcoming event image 2.png' },
    { id: 3, name: 'Code  51', summary: 'Unite under the midnight sky.', date: '2025-10-24', flyer: 'assets/upcoming events images/upcoming event image 3.png' },
    { id: 4, name: 'Code  51', summary: 'The party after the party.', date: '2025-12-07', flyer: 'assets/upcoming events images/upcoming event image 4.png' },

  ];
    
  private previousEvents: Event[] = [
    { id: 10, name: 'AREA 51', summary: 'A night of old-school jams.', date: '2025-08-01', flyer: 'assets/previous events images/previous event image 1.jpg' },
    { id: 11, name: 'AREA 51', summary: 'Headphones on, world off.', date: '2025-08-01', flyer: 'assets/previous events images/previous event image 2.jpg' },
    { id: 12, name: 'AREA 51', summary: 'Headphones on, world off.', date: '2025-08-01', flyer: 'assets/previous events images/previous event image 3.jpg' },
    { id: 13, name: 'AREA 51', summary: 'Headphones on, world off.', date: '2025-08-01', flyer: 'assets/previous events images/previous event image 4.jpg' },
    { id: 14, name: 'AREA 51', summary: 'Headphones on, world off.', date: '2025-08-01', flyer: 'assets/previous events images/previous event image 5.jpg' },
    { id: 15, name: 'AREA 51', summary: 'Headphones on, world off.', date: '2025-08-01', flyer: 'assets/previous events images/previous event image 6.jpg' },
  ];

  getUpcomingEvents() {
    const today = new Date().toISOString().substring(0, 10);
    return this.events.filter(e => e.date >= today);
  }

  getPreviousEvents() {
    const today = new Date().toISOString().substring(0, 10);
    return this.previousEvents.filter(e => e.date < today);
  }

  getEventsByDate(date: string) {
    const selectedDayOfWeek = new Date(date).toLocaleString('en-US', { weekday: 'long' });
    return this.events.filter(e => e.date === date || (e.isRecurring && e.day === selectedDayOfWeek));
  }

  addEvent(event: Event) {
    const newId = this.events.length > 0 ? Math.max(...this.events.map(e => e.id)) + 1 : 1;
    this.events.push({ ...event, id: newId });
  }
}