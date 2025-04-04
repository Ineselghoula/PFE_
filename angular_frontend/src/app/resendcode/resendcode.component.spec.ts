import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ResendcodeComponent } from './resendcode.component';

describe('ResendcodeComponent', () => {
  let component: ResendcodeComponent;
  let fixture: ComponentFixture<ResendcodeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ResendcodeComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ResendcodeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
