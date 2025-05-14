import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { EvenementService } from '../evenement.service';
import { Chart, registerables } from 'chart.js';
import { FormGroup, FormBuilder } from '@angular/forms';

@Component({
  selector: 'app-organizator-dashboard',
  templateUrl: './organizator-dashboard.component.html',
  styleUrls: ['./organizator-dashboard.component.css']
})
export class OrganizatorDashboardComponent implements OnInit {
@ViewChild('monthlySalesChart') monthlySalesChartRef!: ElementRef;
  @ViewChild('categoryChart') categoryChartRef!: ElementRef;
  
  dashboardData: any;
  loading = true;
  error: string | null = null;
  filterForm: FormGroup;

  constructor(
    private evenementService: EvenementService,
    private fb: FormBuilder
  ) {
    Chart.register(...registerables);
    this.filterForm = this.fb.group({
      searchTerm: [''],
      dateRange: this.fb.control({ value: '', disabled: false }),
      statusFilter: this.fb.control({ value: 'all', disabled: false }),
      timeframe: this.fb.control({ value: 'monthly', disabled: false })
    });
  }

  ngOnInit(): void {
    this.loadDashboardData();
    this.loadCategoryStats();
    this.setupFormListeners();
  }

  setupFormListeners(): void {
    this.filterForm.get('timeframe')?.valueChanges.subscribe(timeframe => {
      this.loadSalesData(timeframe);
    });
  }

  loadDashboardData(): void {
    this.loading = true;
    this.error = null;
    
    this.evenementService.getOrganisateurDashboard().subscribe({
      next: (data) => {
        this.dashboardData = data;
        this.loading = false;
        this.renderMonthlySalesChart();
      },
      error: (err) => {
        this.error = 'Erreur lors du chargement des données';
        this.loading = false;
        console.error(err);
      }
    });
  }

  loadCategoryStats(): void {
    this.evenementService.eventPercentageByCategory().subscribe({
      next: (data) => {
        this.renderCategoryChart(data);
      },
      error: (err) => {
        console.error('Failed to load category stats', err);
      }
    });
  }

  loadSalesData(timeframe: string): void {
    this.evenementService.getSalesData(timeframe).subscribe({
      next: (data) => {
        this.dashboardData.sales_data = data.sales_data;
        this.dashboardData.timeframe = data.timeframe;
        this.renderMonthlySalesChart();
      },
      error: (err) => {
        console.error('Failed to load sales data', err);
      }
    });
  }

  renderMonthlySalesChart(): void {
    if (!this.dashboardData?.sales_data) return;

    const canvas = this.monthlySalesChartRef.nativeElement;
    const existingChart = Chart.getChart(canvas);
    if (existingChart) {
      existingChart.destroy();
    }

    const labels = this.dashboardData.sales_data.map((item: any) => {
      if (this.dashboardData.timeframe === 'monthly') {
        return new Date(2000, item.month - 1).toLocaleString('default', { month: 'short' });
      }
      return item.date || item.week || item.year;
    });

    const data = this.dashboardData.sales_data.map((item: any) => item.revenue);

    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Revenu (TND)',
          data: data,
          backgroundColor: 'rgba(54, 162, 235, 0.5)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

  renderCategoryChart(categoryData: any): void {
    if (!categoryData?.categories) return;

    const canvas = this.categoryChartRef.nativeElement;
    const existingChart = Chart.getChart(canvas);
    if (existingChart) {
      existingChart.destroy();
    }

    const labels = categoryData.categories.map((cat: any) => cat.category_name);
    const data = categoryData.categories.map((cat: any) => cat.percentage);
    const backgroundColors = [
      'rgba(255, 99, 132, 0.7)',
      'rgba(54, 162, 235, 0.7)',
      'rgba(255, 206, 86, 0.7)',
      'rgba(75, 192, 192, 0.7)',
      'rgba(153, 102, 255, 0.7)',
      'rgba(255, 159, 64, 0.7)'
    ];

    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: backgroundColors,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
          },
          tooltip: {
            callbacks: {
              label: (context) => {
                return `${context.label}: ${context.raw}%`;
              }
            }
          }
        }
      }
    });
  }

  applyFilters(): void {
    const filters = this.filterForm.getRawValue();
    // Implémentez la logique de filtrage ici
    console.log('Filters applied:', filters);
  }

  resetFilters(): void {
    this.filterForm.reset({
      searchTerm: '',
      dateRange: '',
      statusFilter: 'all',
      timeframe: 'monthly'
    });
  }

  refreshData(): void {
    this.loadDashboardData();
    this.loadCategoryStats();
  }

  toggleDateFilter(): void {
    const dateRangeControl = this.filterForm.get('dateRange');
    if (dateRangeControl?.disabled) {
      dateRangeControl.enable();
    } else {
      dateRangeControl?.disable();
    }
  }
}