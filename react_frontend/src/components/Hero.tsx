import { ArrowRight, CheckCircle } from 'lucide-react';
import { ImageWithFallback } from './figma/ImageWithFallback';

interface HeroProps {
  onGetStarted: () => void;
}

export function Hero({ onGetStarted }: HeroProps) {
  return (
    <section className="container mx-auto px-6 py-20 md:py-32">
      <div className="grid md:grid-cols-2 gap-12 items-center">
        <div>
          <div className="inline-block px-4 py-2 bg-blue-50 text-blue-600 rounded-full mb-6">
            CRM for Small Businesses
          </div>
          <h1 className="text-gray-900 mb-6">
            Manage Your Business with Confidence
          </h1>
          <p className="text-gray-600 mb-8">
            Streamline your customer relationships, automate invoicing, and grow your business with our all-in-one CRM solution built specifically for small businesses.
          </p>
          
          <div className="space-y-3 mb-8">
            <div className="flex items-center gap-3">
              <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0" />
              <span className="text-gray-700">Track customers and contracts effortlessly</span>
            </div>
            <div className="flex items-center gap-3">
              <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0" />
              <span className="text-gray-700">Automated invoicing and payment collection</span>
            </div>
            <div className="flex items-center gap-3">
              <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0" />
              <span className="text-gray-700">Centralized contact management</span>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row gap-4">
            <button
              onClick={onGetStarted}
              className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
            >
              Get Started
              <ArrowRight className="w-5 h-5" />
            </button>
            <button className="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-gray-400 transition-colors">
              Watch Demo
            </button>
          </div>
        </div>

        <div className="relative">
          <div className="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl">
            <ImageWithFallback
              src="https://images.unsplash.com/photo-1579389248774-07907f421a6b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzbWFsbCUyMGJ1c2luZXNzJTIwdGVhbSUyMG1lZXRpbmd8ZW58MXx8fHwxNzY1NjAyOTYwfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
              alt="Business team collaboration"
              className="w-full h-full object-cover"
            />
          </div>
          {/* Floating card */}
          <div className="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-lg p-4 border border-gray-100">
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <span className="text-green-600">✓</span>
              </div>
              <div>
                <p className="text-gray-900">Invoice Sent</p>
                <p className="text-gray-500">Payment processing...</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
