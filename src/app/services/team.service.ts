import { Injectable } from '@angular/core';

export interface TeamMember {
  name: string;
  title: string;
  role: string;
  jobDescription: string;
  contact: string;
  socials: string;
  email: string;
}

@Injectable({
  providedIn: 'root'
})
export class TeamService {

  submitApplication(member: TeamMember) {
    console.log('Application submitted successfully:', member);
    console.log(`Email sent to ${member.email}: Your application was successful!`);
    console.log(`Email sent to company: New application from ${member.name}.`);
    alert('Thank you for your application! A confirmation email has been sent.');
  }
}