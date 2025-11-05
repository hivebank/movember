import { loadStripe } from '@stripe/stripe-js'

let stripePromise = null

export const getStripe = () => {
  if (!stripePromise) {
    stripePromise = loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY)
  }
  return stripePromise
}

export const redirectToCheckout = async (sessionId) => {
  const stripe = await getStripe()
  const { error } = await stripe.redirectToCheckout({ sessionId })

  if (error) {
    console.error('Stripe redirect error:', error)
    throw error
  }
}
