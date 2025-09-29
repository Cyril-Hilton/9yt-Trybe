import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Job, JobsService } from 'src/app/services/jobs.service';

@Component({
  selector: 'app-jobs',
  templateUrl: './jobs.component.html',
  styleUrls: ['./jobs.component.scss']
})
export class JobsComponent implements OnInit {
  jobForm: FormGroup;
  jobTypes: string[] = [];
  professionals: Job[] = [];

  constructor(private fb: FormBuilder, private jobsService: JobsService) {
    this.jobForm = this.fb.group({
      firstName: ['', Validators.required],
      lastName: ['', Validators.required],
      middleName: [''],
      title: ['', Validators.required],
      jobType: ['', Validators.required],
      portfolioLink: ['', Validators.required],
      pic: ['', Validators.required],
    });
  }

  ngOnInit(): void {
    this.jobTypes = this.jobsService.getJobTypes();
    this.professionals = this.jobsService.getProfessionals();
  }

  addJob() {
    if (this.jobForm.valid) {
      const newJob = {
        ...this.jobForm.value,
        name: `${this.jobForm.value.firstName} ${this.jobForm.value.middleName || ''} ${this.jobForm.value.lastName}`.trim()
      };
      this.jobsService.addProfessional(newJob);
      alert('Your job profile has been added!');
      this.jobForm.reset();
    }
  }
}