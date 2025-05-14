import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OrganizatorDashboardComponent } from './organizator-dashboard.component';

describe('OrganizatorDashboardComponent', () => {
  let component: OrganizatorDashboardComponent;
  let fixture: ComponentFixture<OrganizatorDashboardComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ OrganizatorDashboardComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OrganizatorDashboardComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
