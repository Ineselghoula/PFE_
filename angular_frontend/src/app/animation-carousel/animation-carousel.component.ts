import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-animation-carousel',
  templateUrl: './animation-carousel.component.html',
  styleUrls: ['./animation-carousel.component.css']
})
export class AnimationCarouselComponent implements OnInit {
  images = [
    '../../assets/image/event1.jpg',
    '../../assets/image/12.jpg',
    '../../assets/image/5.jpg',
     '../../assets/image/1-21.png'

  ];
  currentIndex = 0;
  interval: any;

  constructor() { }

  ngOnInit(): void {
    this.startAutoRotation();
  }

  startAutoRotation(): void {
    this.interval = setInterval(() => {
      this.nextSlide();
    }, 3000); // Change d'image toutes les 3 secondes
  }

  nextSlide(): void {
    this.currentIndex = (this.currentIndex + 1) % this.images.length;
  }

  prevSlide(): void {
    this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
  }

  goToSlide(index: number): void {
    this.currentIndex = index;
    // Réinitialise le timer quand on clique manuellement
    clearInterval(this.interval);
    this.startAutoRotation();
  }

  ngOnDestroy(): void {
    clearInterval(this.interval); // Nettoie l'intervalle quand le composant est détruit
  }
}