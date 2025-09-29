import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { TeamService } from 'src/app/services/team.service';

@Component({
  selector: 'app-team',
  templateUrl: './team.component.html',
  styleUrls: ['./team.component.scss']
})
export class TeamComponent {
  teamForm: FormGroup;

  constructor(
    private fb: FormBuilder,
    private teamService: TeamService
  ) {
    this.teamForm = this.fb.group({
      name: ['', Validators.required],
      title: ['', Validators.required],
      role: ['Volunteer', Validators.required],
      jobDescription: ['', Validators.required],
      contact: ['', Validators.required],
      socials: [''],
      email: ['', [Validators.required, Validators.email]]
    });
  }

  onSubmit() {
    if (this.teamForm.valid) {
      this.teamService.submitApplication(this.teamForm.value);
      this.teamForm.reset({ role: 'Volunteer' });
    }
  }
}