import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

export interface MerchItem {
  id: number;
  name: string;
  price: number;
  image: string;
}

export interface CartItem extends MerchItem {
  quantity: number;
}

@Injectable({
  providedIn: 'root'
})
export class ShopService {
  private products: MerchItem[] = [
    { id: 1, name: '9yt !Trybe shirt ', price: 150, image: 'assets/product images/9yt !Trybe shirt sample.png' },
    { id: 2, name: '9yt !Trybe hoodie ', price: 250, image: 'assets/product images/9yt !Trybe hoodie sample.png' },
    { id: 3, name: '9yt !Trybe cap ', price: 80, image: 'assets/product images/9yt !Trybe cap sample.png' },
    { id: 4, name: '9yt !Trybe sweat pants ', price: 80, image: 'assets/product images/9yt !Trybe sweat pants sample.png' },
    { id: 5, name: '9yt !Trybe tote bag ', price: 80, image: 'assets/product images/9yt !Trybe tote bag sample.png' },
    { id: 6, name: '9yt !Trybe bikini ', price: 80, image: 'assets/product images/9yt !Trybe bikini sample.png' },
    { id: 7, name: '9yt !Trybe bikini 2 ', price: 80, image: 'assets/product images/9yt !Trybe bikini 2 sample.png' },
    { id: 8, name: '9yt !Trybe shoes ', price: 80, image: 'assets/product images/9yt !Trybe shoes sample.png' },
    { id: 9, name: '9yt !Trybe sneaker ', price: 80, image: 'assets/product images/9yt !Trybe sneaker sample.png' },
    { id: 10, name: '9yt !Trybe jeans ', price: 80, image: 'assets/product images/9yt !Trybe jeans sample.png' },

  ];
  private cart: CartItem[] = [];
  private cartSubject = new BehaviorSubject<CartItem[]>([]);

  getProducts() {
    return this.products;
  }

  getCart(): Observable<CartItem[]> {
    return this.cartSubject.asObservable();
  }

  addToCart(item: MerchItem) {
    const existingItem = this.cart.find(cartItem => cartItem.id === item.id);
    if (existingItem) {
      existingItem.quantity++;
    } else {
      this.cart.push({ ...item, quantity: 1 });
    }
    this.cartSubject.next(this.cart);
  }

  updateQuantity(itemId: number, quantity: number) {
    const item = this.cart.find(cartItem => cartItem.id === itemId);
    if (item) {
      item.quantity = quantity;
      if (item.quantity <= 0) {
        this.cart = this.cart.filter(cartItem => cartItem.id !== itemId);
      }
      this.cartSubject.next(this.cart);
    }
  }

  clearCart() {
    this.cart = [];
    this.cartSubject.next(this.cart);
  }

  // Dummy payment processing
  processPayment(billingInfo: any) {
    console.log('Processing payment...');
    console.log('Billing Info:', billingInfo);
    console.log('Payment successful!');
  }
}