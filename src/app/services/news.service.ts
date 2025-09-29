import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';

export interface NewsItem {
  title: string;
  image: string;
  source: string;
}

@Injectable({
  providedIn: 'root'
})
export class NewsService {
  private dummyNews: NewsItem[] = [
    { title: 'Gamerz Hive to Host CODE 51 Gaming Convention', image: 'assets/magazine images/magazine image 2.jpg', source: 'tiktok.com/@9yt.trybe'},
    { title: 'Passon DJ rocks the dome at CODE 51', image: 'assets/magazine images/magazine image 1.jpg', source: 'tiktok.com/@9yt.trybe' },
    { title: 'Godson Eswag stuns the crowd at CODE 51', image: 'assets/magazine images/magazine image 4.jpg', source: 'tiktok.com/@9yt.trybe' },
  ];

  getEntertainmentNews(): Observable<NewsItem[]> {
    return of(this.dummyNews);
  }
}