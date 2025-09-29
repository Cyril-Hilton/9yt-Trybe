import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { EventService } from 'src/app/services/event.service';

@Component({
  selector: 'app-calendar',
  templateUrl: './calendar.component.html',
  styleUrls: ['./calendar.component.scss']
})
export class CalendarComponent implements OnInit {
  @Output() dateSelected = new EventEmitter<Date>();
  
  currentDate = new Date();
  currentMonthName!: string;
  currentYear!: number;
  calendarDays: (number | null)[] = [];
  dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  eventDates: string[] = [];

  constructor(private eventService: EventService) { }

  ngOnInit(): void {
    this.updateCalendar();
  }

  updateCalendar(): void {
    this.currentMonthName = this.currentDate.toLocaleString('default', { month: 'long' });
    this.currentYear = this.currentDate.getFullYear();
    this.calendarDays = this.getCalendarDays(this.currentDate.getFullYear(), this.currentDate.getMonth());
    this.eventDates = this.eventService.getUpcomingEvents().map(e => e.date);
  }

  getCalendarDays(year: number, month: number): (number | null)[] {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const days = [];

    for (let i = 0; i < firstDay; i++) {
      days.push(null);
    }
    for (let i = 1; i <= daysInMonth; i++) {
      days.push(i);
    }
    return days;
  }

  changeMonth(delta: number): void {
    this.currentDate.setMonth(this.currentDate.getMonth() + delta);
    this.updateCalendar();
  }

  selectDate(day: number | null): void {
    if (day !== null) {
      const selectedDate = new Date(this.currentYear, this.currentDate.getMonth(), day);
      this.dateSelected.emit(selectedDate);
    }
  }

  hasEvent(day: number | null): boolean {
    if (day === null) return false;
    const dateString = `${this.currentYear}-${(this.currentDate.getMonth() + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
    return this.eventDates.includes(dateString);
  }
}