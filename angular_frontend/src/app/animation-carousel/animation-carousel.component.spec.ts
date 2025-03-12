import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AnimationCarouselComponent } from './animation-carousel.component';

describe('AnimationCarouselComponent', () => {
  let component: AnimationCarouselComponent;
  let fixture: ComponentFixture<AnimationCarouselComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AnimationCarouselComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AnimationCarouselComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
