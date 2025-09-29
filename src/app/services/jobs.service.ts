import { Injectable } from '@angular/core';

export interface Job {
  id: number;
  name: string;
  title: string;
  jobType: string;
  portfolioLink: string;
  pic: string;
}

@Injectable({
  providedIn: 'root'
})
export class JobsService {
  private professionals: Job[] = [
    { id: 1, name: 'Alex Doe', title: 'Senior Videographer', jobType: 'Videographer', portfolioLink: 'assets/team members/team member 1.png', pic: 'assets/team members/team member 1.png' },
    { id: 2, name: 'Jane Smith', title: 'Creative Designer', jobType: 'Graphics Designer', portfolioLink: 'assets/team members/team member 2.png', pic: 'assets/team members/team member 2.png' },
    { id: 3, name: 'Chris Evans', title: 'Event Setup Manager', jobType: 'Event Setups', portfolioLink: 'assets/team members/team member 3.png', pic: 'assets/team members/team member 3.png' },
  ];

  getProfessionals() {
    return this.professionals;
  }

  addProfessional(job: Job) {
    const newId = this.professionals.length > 0 ? Math.max(...this.professionals.map(j => j.id)) + 1 : 1;
    this.professionals.push({ ...job, id: newId });
  }

  getJobTypes() {
    return ['Photographer', 'Event Planner', 'Videographer', 'Graphics Designer', 'Fashion Designer', 'Event Setups', '3D/4D Modeling', 'Animator', 'Web Designer'];
  }
}