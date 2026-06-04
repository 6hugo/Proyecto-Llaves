import { Component, inject, OnInit, signal, ChangeDetectionStrategy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FaqService } from '../../services/faq.service';
import { Faq } from '../../models/shop.models';

@Component({
  selector: 'app-faq-section',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './faq-section.component.html',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class FaqSectionComponent implements OnInit {
  private faqService = inject(FaqService);
  faqs = signal<Faq[]>([]);
  expandedFaq = signal<number | null>(null);
  isLoading = signal(true);

  ngOnInit() {
    console.log('FaqSectionComponent inicializado');
    this.loadFaqs();
  }

  loadFaqs() {
    this.isLoading.set(true);
    console.log('Iniciando carga de FAQs...');
    this.faqService.getActiveFaqs().subscribe({
      next: (faqs) => {
        // Eliminar duplicados usando el contenido de la pregunta
        const uniqueMap = new Map();

        if (faqs && Array.isArray(faqs)) {
          faqs.forEach(f => {
            const contentKey = f.question.toLowerCase().trim();
            if (!uniqueMap.has(contentKey)) {
              uniqueMap.set(contentKey, f);
            }
          });
        }

        const unique = Array.from(uniqueMap.values());

        console.log('FAQs recibidas del servicio:', faqs);
        console.log('Número de FAQs (unicas):', unique.length);
        this.faqs.set(unique);
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error('Error cargando FAQs:', err);
        this.isLoading.set(false);
      }
    });
  }

  toggleFaq(id: number) {
    this.expandedFaq.set(this.expandedFaq() === id ? null : id);
  }
}
