import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { map } from 'rxjs/operators';

export interface ChatMessage {
  text: string;
  isUser: boolean;
}

export interface NewsItem {
  title: string;
  source: string;
  image: string;
}

export interface SearchResult {
  title: string;
  snippet: string;
  url: string;
}

@Injectable({
  providedIn: 'root'
})
export class GeminiService {
  // IMPORTANT: You need to create a simple backend (e.g., Node.js with Express)
  // to handle the actual API calls to Gemini. This URL should point to your backend.
  private apiUrl = 'YOUR_BACKEND_API_URL_HERE'; 

  constructor(private http: HttpClient) { }

  // Method for the Chatbot
  // The backend will send the user's message to the Gemini API and return the response.
  sendChatMessage(message: string): Observable<ChatMessage> {
    // This is a placeholder. You will replace this with an actual HTTP POST request to your backend.
    console.log(`Sending message to Gemini: "${message}"`);
    
    // Simulate a response for now
    return of({
      text: `Hello! I'm your AI assistant. How can I help you with your night, your tribe, or your experience?`,
      isUser: false
    });
    
    // Example of a real request (uncomment when you have a backend):
    // return this.http.post<any>(`${this.apiUrl}/chat`, { message }).pipe(
    //   map(response => ({ text: response.text, isUser: false }))
    // );
  }

  // Method for In-App Search
  // The backend will take the query and ask Gemini to search for relevant info related to your site.
  searchSite(query: string): Observable<SearchResult[]> {
    console.log(`Searching the site for: "${query}"`);

    // Simulate a response for now
    const mockResults: SearchResult[] = [
      { title: 'About the 9yt Trybe', snippet: 'Learn about our mission and our team members.', url: '/about-us' },
      { title: 'Upcoming Events', snippet: 'Find out about our latest parties and gatherings!', url: '/events' },
      { title: 'Join the Trybe', snippet: 'Explore job opportunities and become part of our team.', url: '/jobs' },
    ];
    return of(mockResults);

    // Example of a real request (uncomment when you have a backend):
    // return this.http.post<any>(`${this.apiUrl}/search`, { query }).pipe(
    //   map(response => response.results)
    // );
  }

  // Method for dynamic News
  // The backend will ask Gemini to generate fresh news headlines and content.
  fetchEntertainmentNews(): Observable<NewsItem[]> {
    console.log('Fetching fresh news from Gemini...');
    
    // Simulate a response for now
    const mockNews: NewsItem[] = [
        { title: 'Gamerz Hive to Host CODE 51 Gaming Convention', image: 'assets/magazine images/magazine image 2.jpg', source: 'tiktok.com/@9yt.trybe'},
        { title: 'Passon DJ rocks the dome at CODE 51', image: 'assets/magazine images/magazine image 1.jpg', source: 'tiktok.com/@9yt.trybe' },
        { title: 'Godson Eswag stuns the crowd at CODE 51', image: 'assets/magazine images/magazine image 4.jpg', source: 'tiktok.com/@9yt.trybe' },
        { title: 'Gamerz Hive to Host CODE 51 Gaming Convention', image: 'assets/magazine images/magazine image 2.jpg', source: 'tiktok.com/@9yt.trybe'},
        { title: 'Passon DJ rocks the dome at CODE 51', image: 'assets/magazine images/magazine image 1.jpg', source: 'tiktok.com/@9yt.trybe' },
        { title: 'Godson Eswag stuns the crowd at CODE 51', image: 'assets/magazine images/magazine image 4.jpg', source: 'tiktok.com/@9yt.trybe' },
        { title: 'Gamerz Hive to Host CODE 51 Gaming Convention', image: 'assets/magazine images/magazine image 2.jpg', source: 'tiktok.com/@9yt.trybe'},
        { title: 'Passon DJ rocks the dome at CODE 51', image: 'assets/magazine images/magazine image 1.jpg', source: 'tiktok.com/@9yt.trybe' },
        { title: 'Godson Eswag stuns the crowd at CODE 51', image: 'assets/magazine images/magazine image 4.jpg', source: 'tiktok.com/@9yt.trybe' },
      ];
    return of(mockNews);

    // Example of a real request (uncomment when you have a backend):
    // return this.http.get<any>(`${this.apiUrl}/news`).pipe(
    //   map(response => response.news)
    // );
  }
}