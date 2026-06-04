import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FaqSectionComponent } from '../faq-section/faq-section.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [RouterLink, FaqSectionComponent],
  templateUrl: './home.component.html'
})
export class HomeComponent {}
